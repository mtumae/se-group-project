<?php 
class Email{
    
public $verificationEmailTemplate=[
    "subject"=>"2 Factor Verification Code",
    "body"=>"
    <div style='padding: 20px;  box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.2), 0 2px 20px 0 rgba(0, 0, 0, 0.19);  border-radius: 20px; background-color:#d6dcdc; text-align: center;'>
        <div style='color: #6E5BAA; display: block; font-family: hybrea, proxima-nova, 'helvetica neue', helvetica, arial, geneva, sans-serif; font-size: 32px; font-weight: 200;'>
            <p>Account verification code:</p>
            <h1>{{VERIFICATION_CODE}}</h1>
            <p>This code will expire soon.</p>
        </div>
    </div>
    "
];

}

