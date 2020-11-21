import 'dart:math';

import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/environment.dart';
import 'package:twilio_flutter/twilio_flutter.dart';

class SmsHelper {
  TwilioFlutter twilioFlutter;

  SmsHelper() {
    twilioFlutter =
        TwilioFlutter(accountSid: env.twilioAccountSid, authToken: env.twilioAuthToken, twilioNumber: env.twilioNumber);
  }

  Future handleSmsVerification(
    TextEditingController phoneController,
    TextEditingController smsVerificationController,
    GlobalKey<FormState> formKey,
    BuildContext context,
    Function phoneNumberVerifiedCallback,
  ) async {
    // verify phone number
    String phoneNumber = phoneController.text;
    String verificationCode = generateVerificationCode();

    await twilioFlutter.sendSMS(
        toNumber: phoneNumber, messageBody: 'Your Resold verification code is: $verificationCode');

    showDialog<void>(
        context: context,
        barrierDismissible: false,
        builder: (BuildContext context) {
          return AlertDialog(
            title: Text('Verify Phone Number'),
            content: SingleChildScrollView(
              child: ListBody(
                children: <Widget>[
                  Form(
                    key: formKey,
                    child: TextFormField(
                        controller: smsVerificationController,
                        keyboardType: TextInputType.number,
                        obscureText: true,
                        decoration: InputDecoration(
                            labelText: 'Enter SMS verification code *',
                            labelStyle: TextStyle(color: ResoldBlue),
                            enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                            focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                            border: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5))),
                        validator: (value) {
                          if (value.isEmpty || value != verificationCode) {
                            return 'Verification code is invalid.';
                          }
                          return null;
                        },
                        style: TextStyle(color: Colors.black)),
                  )
                ],
              ),
            ),
            actions: <Widget>[
              FlatButton(
                child: Text(
                  'OK',
                  style: TextStyle(color: ResoldBlue),
                ),
                onPressed: () async {
                  if (formKey.currentState.validate()) {
                    Navigator.of(context, rootNavigator: true).pop('dialog');
                    phoneNumberVerifiedCallback();
                  } // end if valid verification code
                },
              ),
              FlatButton(
                child: Text(
                  'Cancel',
                  style: TextStyle(color: ResoldBlue),
                ),
                onPressed: () {
                  smsVerificationController.value = TextEditingValue();
                  Navigator.of(context, rootNavigator: true).pop('dialog');
                },
              ),
            ],
          );
        });
  } // end function handleSmsVerification

  String generateVerificationCode() {
    var rng = new Random();
    int numDigits = 6;
    String verificationCode = '';
    for (var i = 0; i < numDigits; i++) {
      verificationCode += rng.nextInt(9).toString();
    }
    return verificationCode;
  } // end function generateVerificationCode

}
