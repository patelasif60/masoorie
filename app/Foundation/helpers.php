<?php
use Carbon\Carbon;

function http_post($SenderID,$mobileno,$TemplateID,$EntityID,$Msg)
{     
    $Password=env('INHOUSE_PASSWORD');
    $UserID=env('INHOUSE_USERID');
    $Phno= $mobileno;
    $ch='';
    $inhouselink = env('INHOUSE_LINK');
    $url=$inhouselink.'?UserID='.$UserID.'&Password='.$Password.'&SenderID='.$SenderID.'&Phno='.$Phno.'&Msg='.urlencode($Msg).'&EntityID='.$EntityID.'&TemplateID='.$TemplateID;
    $ch = curl_init($url);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    $output=curl_exec($ch);
    curl_close($ch);
    return $output;
}


if (!function_exists('uploadImageToLocal')) {
    function uploadImageToLocal($image, $imagePath)
    {
        $imageFileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $imageFileExtension = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        $imageFileName = formatFileName($imageFileName);
        $imageFile = $imageFileName . '_' . now()->timestamp . '.' . $imageFileExtension;

        // Define the full path where the image will be stored in the storage/app/public directory
        $path = storage_path('app/public' . $imagePath . $imageFile);

        // Save the image to the storage/app/public directory
        $image->storeAs($imagePath, $imageFile, 'public');

        // Use the storage disk URL to generate the URL
        $imageUrl = Storage::disk('public')->url($imagePath . $imageFile);

        $imageData = [
            'url' => $imageUrl,
            'file_name' => $imageFile,
        ];

        return $imageData;
    }
}
if (!function_exists('formatFileName')) {
    function formatFileName($fileName) {
        return preg_replace("![^a-z0-9]+!i", "_",  $fileName);
    }
}
if (!function_exists('uploadBase64ToS3')) {
    function uploadBase64ToLocal($image, $imagePath)
    {
        // Generate a unique filename
        $imageFile = rand(1, 9999999) . '_' . now()->timestamp . '.png';

        // Define the full path where the image will be stored in the storage/app/public directory
        $path = public_path('images' . $imagePath . $imageFile);

        // Decode the base64 image data
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));

        // Save the image to the storage/app/public directory
        file_put_contents($path, $data);

        // Use the storage disk URL to generate the URL
        $imageUrl = Storage::disk('local')->url($imagePath . $imageFile);

        $imageData = [
            'url' => $imageUrl,
            'file_name' => $imageFile,
        ];

        return $imageData;
    }
}
if (!function_exists('generateDateArray')) {
    function generateDateArray($startDateStr, $endDateStr) {
        $startDate = Carbon::createFromFormat('d-m-Y', $startDateStr);
        $endDate = Carbon::createFromFormat('d-m-Y', $endDateStr);
        
        $dateArray = [];

        while ($startDate->lte($endDate)) {
            $dateArray[] = $startDate->format('d-m-Y');
            $startDate->addDay();
        }

        return $dateArray;
    }
}