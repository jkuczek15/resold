import 'package:flutter/material.dart';
import 'package:resold/constants/dev-constants.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/environment.dart';
import 'package:resold/helpers/sms-helper.dart';
import 'package:resold/screens/account/edit-address.dart';
import 'package:resold/screens/home.dart';
import 'package:geolocator/geolocator.dart';
import 'package:geocoder/geocoder.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/state/actions/init-state.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/view-models/request/magento/customer-request.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/models/customer/customer-address.dart';
import 'package:resold/widgets/loading.dart';
import 'package:twilio_flutter/twilio_flutter.dart';

class SignUpPage extends StatefulWidget {
  final Function dispatcher;

  SignUpPage({Function dispatcher, Key key})
      : dispatcher = dispatcher,
        super(key: key);

  @override
  SignUpPageState createState() => SignUpPageState(dispatcher);
}

class SignUpPageState extends State<SignUpPage> {
  final firstNameController = TextEditingController();
  final lastNameController = TextEditingController();
  final emailController = TextEditingController();
  final phoneController = TextEditingController();
  final passwordController = TextEditingController();
  final confirmPasswordController = TextEditingController();
  final smsVerificationController = TextEditingController();
  final smsHelper = SmsHelper();
  Future<List<Address>> futureAddresses;
  TwilioFlutter twilioFlutter;
  final smsKey = GlobalKey<FormState>();
  final signupKey = GlobalKey<FormState>();
  final Function dispatcher;

  SignUpPageState(this.dispatcher);

  @override
  void initState() {
    super.initState();
  } // end function initState

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
        child: Scaffold(
            resizeToAvoidBottomInset: false,
            resizeToAvoidBottomPadding: false,
            body: Stack(children: [
              Image.asset('assets/images/login/resold-app-loginpage-background.jpg',
                  fit: BoxFit.cover, alignment: Alignment.topCenter, height: 2000),
              SingleChildScrollView(
                child: Form(
                    key: signupKey,
                    child: Column(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Column(children: [
                            Padding(
                                child: Align(
                                    alignment: Alignment.topCenter,
                                    child: Image.asset('assets/images/resold-white-logo.png',
                                        fit: BoxFit.cover, width: 500)),
                                padding: EdgeInsets.fromLTRB(30, 40, 30, 20)),
                            Row(
                              children: [
                                BackButton(color: Colors.white),
                                Center(
                                    child: Text('Buy & sell with on-demand delivery',
                                        style: new TextStyle(
                                            fontSize: 18.0, fontWeight: FontWeight.bold, color: Colors.white)))
                              ],
                            )
                          ]),
                          Center(
                              child: Column(children: [
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 20, 50, 15),
                                child: TextFormField(
                                  controller: firstNameController,
                                  decoration: InputDecoration(
                                      hintText: 'Enter your first name...',
                                      hintStyle: TextStyle(color: Colors.white),
                                      labelStyle: new TextStyle(
                                        color: ResoldBlue,
                                      ),
                                      enabledBorder: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      ),
                                      focusedBorder:
                                          UnderlineInputBorder(borderSide: BorderSide(color: Colors.black, width: 1.5)),
                                      border: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      )),
                                  style: TextStyle(color: Colors.white),
                                  validator: (value) {
                                    if (value.isEmpty) {
                                      return 'Please enter your first name.';
                                    }
                                    return null;
                                  },
                                )),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 15),
                                child: TextFormField(
                                  controller: lastNameController,
                                  decoration: InputDecoration(
                                      hintText: 'Enter your last name...',
                                      hintStyle: TextStyle(color: Colors.white),
                                      labelStyle: new TextStyle(
                                        color: ResoldBlue,
                                      ),
                                      enabledBorder: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      ),
                                      focusedBorder:
                                          UnderlineInputBorder(borderSide: BorderSide(color: Colors.black, width: 1.5)),
                                      border: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      )),
                                  style: TextStyle(color: Colors.white),
                                  validator: (value) {
                                    if (value.isEmpty) {
                                      return 'Please enter your last name.';
                                    }
                                    return null;
                                  },
                                )),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 15),
                                child: TextFormField(
                                  controller: emailController,
                                  decoration: InputDecoration(
                                      hintText: 'Enter your email...',
                                      hintStyle: TextStyle(color: Colors.white),
                                      labelStyle: new TextStyle(
                                        color: ResoldBlue,
                                      ),
                                      enabledBorder: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      ),
                                      focusedBorder:
                                          UnderlineInputBorder(borderSide: BorderSide(color: Colors.black, width: 1.5)),
                                      border: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      )),
                                  style: TextStyle(color: Colors.white),
                                  validator: (value) {
                                    if (value.isEmpty) {
                                      return 'Please enter your email.';
                                    }
                                    return null;
                                  },
                                )),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 15),
                                child: TextFormField(
                                  controller: phoneController,
                                  keyboardType: TextInputType.number,
                                  decoration: InputDecoration(
                                      hintText: 'Enter your phone number...',
                                      hintStyle: TextStyle(color: Colors.white),
                                      labelStyle: new TextStyle(
                                        color: ResoldBlue,
                                      ),
                                      enabledBorder: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      ),
                                      focusedBorder:
                                          UnderlineInputBorder(borderSide: BorderSide(color: Colors.black, width: 1.5)),
                                      border: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      )),
                                  style: TextStyle(color: Colors.white),
                                  validator: (value) {
                                    if (value.isEmpty) {
                                      return 'Please enter your phone number.';
                                    }
                                    return null;
                                  },
                                )),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 15),
                                child: TextFormField(
                                  obscureText: true,
                                  controller: passwordController,
                                  decoration: InputDecoration(
                                      hintText: 'Enter your password...',
                                      hintStyle: TextStyle(color: Colors.white),
                                      labelStyle: new TextStyle(
                                        color: ResoldBlue,
                                      ),
                                      enabledBorder: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      ),
                                      focusedBorder:
                                          UnderlineInputBorder(borderSide: BorderSide(color: Colors.black, width: 1.5)),
                                      border: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      )),
                                  style: TextStyle(color: Colors.white),
                                  validator: (value) {
                                    if (value.isEmpty) {
                                      return 'Please enter your password.';
                                    }
                                    return null;
                                  },
                                )),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 30),
                                child: TextFormField(
                                  obscureText: true,
                                  controller: confirmPasswordController,
                                  decoration: InputDecoration(
                                      hintText: 'Confirm your password...',
                                      hintStyle: TextStyle(color: Colors.white),
                                      labelStyle: new TextStyle(
                                        color: ResoldBlue,
                                      ),
                                      enabledBorder: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      ),
                                      focusedBorder:
                                          UnderlineInputBorder(borderSide: BorderSide(color: Colors.black, width: 1.5)),
                                      border: UnderlineInputBorder(
                                        borderSide: BorderSide(color: Colors.white, width: 1.5),
                                      )),
                                  style: TextStyle(color: Colors.white),
                                  validator: (value) {
                                    if (value.isEmpty) {
                                      return 'Please confirm your password.';
                                    }
                                    return null;
                                  },
                                )),
                            RaisedButton(
                              shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                              onPressed: () async {
                                if (!signupKey.currentState.validate()) {
                                  return;
                                }
                                // show a loading indicator
                                showDialog(
                                    context: context,
                                    builder: (BuildContext context) {
                                      return Center(child: Loading());
                                    });

                                // get current position
                                await Geolocator()
                                    .getCurrentPosition(desiredAccuracy: LocationAccuracy.high)
                                    .then((location) async {
                                  futureAddresses = Geocoder.local.findAddressesFromCoordinates(
                                      new Coordinates(location.latitude, location.longitude));
                                });

                                // create customer
                                List<Address> addresses = await futureAddresses;
                                Address address = addresses.first;

                                CustomerAddress customerAddress = CustomerAddress.fromAddress(
                                    address, firstNameController.text, lastNameController.text, phoneController.text);
                                String regionId = await Resold.getRegionId(
                                    customerAddress.region.regionCode, customerAddress.countryId);
                                customerAddress.region.regionId = int.parse(regionId);

                                await smsHelper.handleSmsVerification(
                                    phoneController, smsVerificationController, smsKey, context, () async {
                                  CustomerResponse customer = await Magento.createCustomer(
                                      CustomerRequest(
                                          email: emailController.text,
                                          firstname: firstNameController.text,
                                          lastname: lastNameController.text,
                                          addresses: [customerAddress]),
                                      passwordController.text,
                                      confirmPasswordController.text);

                                  if (customer.statusCode == 200) {
                                    // signup was successful
                                    // store to disk
                                    await CustomerResponse.save(customer);

                                    // create a firebase user
                                    await ResoldFirebase.createOrUpdateUser(customer);

                                    // initialize application state
                                    AppState initialState = await AppState.initialState(customer,
                                        currentLocation: env.isDevelopment ? TestLocations.evanston : null);

                                    dispatcher(InitStateAction(initialState));

                                    // navigate
                                    Navigator.of(context, rootNavigator: true).pop('dialog');
                                    Navigator.pop(context);
                                    Navigator.pushReplacement(
                                        context,
                                        PageRouteBuilder(
                                          pageBuilder: (context, animation, secondaryAnimation) => Home(),
                                          transitionsBuilder: (context, animation, secondaryAnimation, child) {
                                            return FadeTransition(opacity: animation, child: child);
                                          },
                                        ));

                                    // show an address dialog
                                    showDialog<void>(
                                      context: context,
                                      barrierDismissible: false,
                                      builder: (BuildContext context) {
                                        return AlertDialog(
                                          title: Text('Save Address'),
                                          content: SingleChildScrollView(
                                            child: ListBody(
                                              children: <Widget>[
                                                Text(
                                                  'We have found this address for your profile. Would you like us to save it?',
                                                ),
                                                SizedBox(height: 15),
                                                Text(customer.addresses.first.toString()),
                                              ],
                                            ),
                                          ),
                                          actions: <Widget>[
                                            FlatButton(
                                              child: Text(
                                                'Save',
                                                style: TextStyle(color: ResoldBlue),
                                              ),
                                              onPressed: () async {
                                                Navigator.of(context, rootNavigator: true).pop('dialog');
                                              },
                                            ),
                                            FlatButton(
                                              child: Text(
                                                'Edit',
                                                style: TextStyle(color: ResoldBlue),
                                              ),
                                              onPressed: () async {
                                                Navigator.of(context, rootNavigator: true).pop('dialog');
                                                Navigator.push(
                                                    context,
                                                    MaterialPageRoute(
                                                        builder: (context) => EditAddressPage(customer, dispatcher)));
                                              },
                                            )
                                          ],
                                        );
                                      },
                                    );
                                  } else {
                                    Navigator.of(context, rootNavigator: true).pop('dialog');
                                    return showDialog<void>(
                                      context: context,
                                      barrierDismissible: false,
                                      builder: (BuildContext context) {
                                        return AlertDialog(
                                          title: Text('Sign Up Error'),
                                          content: SingleChildScrollView(
                                            child: ListBody(
                                              children: <Widget>[Text(customer.error)],
                                            ),
                                          ),
                                          actions: <Widget>[
                                            FlatButton(
                                              child: Text('Ok'),
                                              onPressed: () {
                                                Navigator.of(context).pop();
                                              },
                                            ),
                                          ],
                                        );
                                      },
                                    );
                                  }
                                });
                              },
                              child: Text('Sign Up',
                                  style:
                                      new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                              padding: EdgeInsets.fromLTRB(105, 30, 105, 30),
                              color: Colors.black,
                              textColor: Colors.white,
                            ),
                            SizedBox(height: 10)
                          ])),
                          SizedBox(height: 5)
                        ])),
              )
            ])),
        onWillPop: () async {
          Navigator.pop(context);
          return false;
        });
  } // end function build
}
