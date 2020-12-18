import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/state/actions/init-state.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/view-models/request/magento/login-request.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';

class LoginPage extends StatefulWidget {
  final Function dispatcher;

  LoginPage({Function dispatcher, Key key})
      : dispatcher = dispatcher,
        super(key: key);

  @override
  LoginPageState createState() => LoginPageState(dispatcher);
}

class LoginPageState extends State<LoginPage> {
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final TextEditingController forgotPasswordController = TextEditingController();
  final GlobalKey<FormState> forgotPasswordKey = GlobalKey<FormState>();
  final Function dispatcher;

  LoginPageState(this.dispatcher);

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
        child: Scaffold(
            resizeToAvoidBottomInset: false,
            resizeToAvoidBottomPadding: false,
            body: Stack(children: [
              Image.asset('assets/images/login/resold-app-loginpage-background.jpg', fit: BoxFit.cover, width: 500),
              Column(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Column(children: [
                      Padding(
                          child: Align(
                              alignment: Alignment.topCenter,
                              child: Image.asset('assets/images/resold-white-logo.png', fit: BoxFit.cover, width: 500)),
                          padding: EdgeInsets.fromLTRB(30, 0, 30, 20)),
                      Center(
                          child: Text('Buy & sell with on-demand delivery',
                              style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)))
                    ]),
                    Center(
                        child: Column(children: [
                      Padding(
                          padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                          child: TextField(
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
                              style: TextStyle(color: Colors.white))),
                      Padding(
                          padding: EdgeInsets.fromLTRB(50, 10, 50, 30),
                          child: TextField(
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
                              style: TextStyle(color: Colors.white))),
                      RaisedButton(
                        shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                        onPressed: () async {
                          // show a loading indicator
                          showDialog(
                              context: context,
                              builder: (BuildContext context) {
                                return Center(child: Loading());
                              });

                          // attempt to login
                          CustomerResponse customer = await Magento.loginCustomer(
                              LoginRequest(username: emailController.text, password: passwordController.text));

                          if (customer.statusCode == 200) {
                            // login was successful
                            // store to disk
                            await CustomerResponse.save(customer);

                            // create a firebase user
                            await ResoldFirebase.createOrUpdateUser(customer);

                            // initialize application state
                            dispatcher(InitStateAction(await AppState.initialState(customer)));

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
                          } else {
                            Navigator.of(context, rootNavigator: true).pop('dialog');
                            return showDialog<void>(
                              context: context,
                              barrierDismissible: false,
                              builder: (BuildContext context) {
                                return AlertDialog(
                                  title: Text('Sign In Error'),
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
                        },
                        child: Text('Sign In',
                            style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                        padding: EdgeInsets.fromLTRB(105, 30, 105, 30),
                        color: Colors.black,
                        textColor: Colors.white,
                      ),
                      SizedBox(height: 15),
                      InkWell(
                        onTap: forgotPassword,
                        child: Text('Forgot password?',
                            style: new TextStyle(
                                fontSize: 16.0,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                                decoration: TextDecoration.underline)),
                      ),
                      SizedBox(height: 10)
                    ])),
                    SizedBox(height: 5)
                  ])
            ])),
        onWillPop: () async {
          Navigator.pop(context);
          return false;
        });
  } // end function build

  void forgotPassword() async {
    return showDialog<void>(
        context: context,
        barrierDismissible: false,
        builder: (BuildContext context) {
          return AlertDialog(
              title: Text('Forgot password?'),
              content: SingleChildScrollView(
                child: ListBody(
                  children: <Widget>[
                    Text(
                      'Enter your email and you will receive a link to change your password.',
                    ),
                    Form(
                      key: forgotPasswordKey,
                      child: TextFormField(
                        controller: forgotPasswordController,
                        decoration: InputDecoration(
                          labelText: 'Enter your email...',
                          labelStyle: TextStyle(color: ResoldBlue),
                          enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                          focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                          border: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                        ),
                        style: TextStyle(color: Colors.black),
                        validator: (value) {
                          if (value.isEmpty) {
                            return 'Please enter a valid email address.';
                          }
                          return null;
                        },
                      ),
                    ),
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
                      if (forgotPasswordKey.currentState.validate()) {
                        String email = forgotPasswordController.text;

                        // send forgot password email
                        var response = await Magento.forgotPassword(email);

                        if (response) {
                          await showDialog<void>(
                              context: context,
                              barrierDismissible: false,
                              builder: (BuildContext context) {
                                return AlertDialog(
                                    title: Text("A password reset email has been sent to $email."),
                                    actions: <Widget>[
                                      FlatButton(
                                          child: Text(
                                            'Ok',
                                            style: TextStyle(color: ResoldBlue),
                                          ),
                                          onPressed: () {
                                            Navigator.of(context).pop();
                                          })
                                    ]);
                              });
                          // close the dialog
                          forgotPasswordController.value = TextEditingValue();
                          Navigator.of(context, rootNavigator: true).pop('dialog');
                        } else {
                          await showDialog<void>(
                              context: context,
                              barrierDismissible: false,
                              builder: (BuildContext context) {
                                return AlertDialog(
                                    title: Text("The email address could not be found."),
                                    actions: <Widget>[
                                      FlatButton(
                                          child: Text(
                                            'Ok',
                                            style: TextStyle(color: ResoldBlue),
                                          ),
                                          onPressed: () {
                                            Navigator.of(context).pop();
                                          })
                                    ]);
                              });
                        } // end if we were able to send a password reset email
                      } // end if forgot password valid
                    }),
                FlatButton(
                  child: Text('Cancel', style: TextStyle(color: ResoldBlue)),
                  onPressed: () {
                    forgotPasswordController.value = TextEditingValue();
                    Navigator.of(context).pop();
                  },
                ),
              ]);
        });
  } // end function forgotPassword
}
