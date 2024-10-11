<?php
$valid = false;

if (isset($_COOKIE["captcha"]) && $_COOKIE["captcha"] == "true") {
    $valid = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        // get verify response
        $data = array(
              'secret' => "6Leo6V0qAAAAAJPPa0s_avBXPlVA3xqrAwBl180i",
              'response' => $_POST['g-recaptcha-response']
          );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL,   "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $verifyResponse = curl_exec($verify);
        $responseData = json_decode($verifyResponse);

        if($responseData->success) {
            $valid = true;
            setcookie( "captcha", "true", time() + 3600, "/", "", 1, 1);

            // header("h-captcha-response: valid");
        } else {
            // header("h-captcha-response: invalid");
        }
    } else {
        // header("h-captcha-response: empty");
    }
} else {
    // header("h-captcha-submit: empty");
}

if ($valid){
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    readfile("hashdah.html");

}
else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Are you human?</title>
    <script src="https://www.google.com/recaptcha/api.js"></script>

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        .iframe-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            filter: blur(5px);
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000000 url(diag-ascii.png);
        }
        .modal-dialog {
            width: 90%;
            max-width: 400px;
        }
        .modal-content {
            background: #0f111a;
            border: 2.5px solid #3f4668;
            border-radius: 10px;
            padding: 20px;
        }
        .modal-body {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .g-recaptcha {
            padding: 10px 20px;
            cursor: pointer;
        }
        #captchaForm {
            min-width: 50vw;
        }
        @media (max-width: 768px) {
            .modal-dialog {
                width: 95%;
            }
            .modal-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<div class="overlay">
    <div class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <p style="color: #e0e2eb;">Verify You're Not a Robot</p>
                    <form id="captchaForm" action="" method="post">
                                <input type="hidden" name="original_url" value="/">
                                <button class="g-recaptcha" data-sitekey="6Leo6V0qAAAAADYdpgwuqe0SxvdiUI47cqvldgmC" data-callback="onCaptchaResolved" data-action='submit' data-theme="dark">I'am not Robot</button>
                              </form>
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
    function onCaptchaResolved(response) {
        document.getElementById('captchaForm').submit();
    }
</script>

</body>
</html>
<?php
}
?>
