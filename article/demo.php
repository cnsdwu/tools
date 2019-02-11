<?php

include_once "wxBizDataCrypt.php";


$appid = 'wx97060377dcf9c031';
$sessionKey = 'XO716y+d65GBDVKSW8jEzA==';

$encryptedData="ytz5yoH0IbU2tyc7xDKxV1ODhzxW6QWVIk1p4xrebgLVBAY+WmSL4SEp22baSb3euKp8+oxsoS1rNoYiAF28q07sCap72SmyPHefgrsH3khEIcx8JxTFSHGce83cYIpnX/XrUz8hFzgWYylITEMJOVWQx4UOPe4NyiMVi8D2gXFPeUuYWMIpkNFMEL+2lUa73MaVLQk/xb/opGHQ3RMI0g8YodLLRP20mIfR79sbhOEA3sz1XByNYEKf80vcADKxI8aC5X9vUbb5DaTw2MSliCMyWsBVSxsDYGwoCBPdjAEv+9Y8isKZQuxBA7SMe7TDIL+SUGo2+8x1TfvNa/8qwN+J65SEE3fvR66J4a9TjdQNJ6LJipjhlOJO/yslJLHpLyiZAvZSFauzso8a11sBML96zH6wJHBHoLI1T0Q7oOy77lWPmGPZxNBhj26ikGaiW8EKJTu6fcbF3iGeWqFEd0aDO4V5fddG/WjVBOYQqGw=";

$iv = 'wvXv2TJ7A8inRkQbLsw7ng==';

$pc = new WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    print($data . "\n");
} else {
    print($errCode . "\n");
}
