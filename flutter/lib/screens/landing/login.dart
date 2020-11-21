import 'package:flutter/material.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/state/actions/update-customer.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/view-models/request/magento/login-request.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/firebase.dart';
import 'package:resold/widgets/loading.dart';

class LoginPage extends StatefulWidget {
  LoginPage({Key key}) : super(key: key);

  @override
  LoginPageState createState() => LoginPageState();
}

class LoginPageState extends State<LoginPage> {
  final emailController = TextEditingController();
  final passwordController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return ViewModelSubscriber<AppState, CustomerResponse>(
        converter: (state) => state.customer,
        builder: (context, dispatcher, model) => WillPopScope(
            child: Scaffold(
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
                          child: Text('Buy & sell locally with delivery.',
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
                          CustomerResponse response = await Magento.loginCustomer(
                              LoginRequest(username: emailController.text, password: passwordController.text));

                          if (response.statusCode == 200) {
                            // login was successful
                            // store to disk
                            await CustomerResponse.save(response);

                            // update customer app state
                            dispatcher(UpdateCustomerAction(response));

                            // create a firebase user
                            await Firebase.createUser(response);

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
                                      children: <Widget>[Text(response.error)],
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
                      SizedBox(height: 10)
                    ])),
                    SizedBox(height: 5)
                  ])
            ])),
            onWillPop: () async {
              Navigator.pop(context);
              return false;
            }));
  }
}
