import 'package:flutter/material.dart';
import 'package:resold/pages/signup.dart';
import 'package:resold/pages/login.dart';

class Landing extends StatelessWidget {
  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(home: LandingPage());
  }
}

class LandingPage extends StatefulWidget {
  LandingPage({Key key}) : super(key: key);

  @override
  LandingPageState createState() => LandingPageState();
}

class LandingPageState extends State<LandingPage> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: Stack (
            children: [
              Image.asset('assets/images/login/resold-app-loginpage-background.jpg', fit: BoxFit.cover, width: 500),
              Column (
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Column (
                        children: [
                          Padding (
                              child: Align(alignment: Alignment.topCenter, child: Image.asset('assets/images/resold-white-logo.png', fit: BoxFit.cover, width: 500)),
                              padding: EdgeInsets.fromLTRB(30, 0, 30, 20)
                          ),
                          Center(
                              child: Text('Buy and sell locally with delivery.',
                              style: new TextStyle(
                                  fontSize: 20.0,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white
                              ))
                          )
                        ]
                    ),
                    Center(
                        child: Column (
                            children: [
                              RaisedButton(
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadiusDirectional.circular(8)
                                ),
                                onPressed: () async {
                                  //after the login REST api call && response code ==200
                                  Navigator.push(context, PageRouteBuilder(
                                    pageBuilder: (context, animation, secondaryAnimation) => SignUpPage(),
                                    transitionsBuilder: (context, animation, secondaryAnimation, child) {
                                      return FadeTransition (
                                        opacity: animation,
                                        child: child
                                      );
                                    },
                                  ));
                                },
                                child: Text('Get Started',
                                    style: new TextStyle(
                                      fontSize: 20.0,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.white
                                    )
                                ),
                                padding: EdgeInsets.fromLTRB(105, 30, 105, 30),
                                color: Colors.black,
                                textColor: Colors.white,
                              ),
                              SizedBox(height: 10),
                              RaisedButton(
                                  shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadiusDirectional.circular(8)
                                  ),
                                  onPressed: () async {
                                    //after the login REST api call && response code ==200
                                    Navigator.push(context, PageRouteBuilder(
                                      pageBuilder: (context, animation, secondaryAnimation) => LoginPage(),
                                      transitionsBuilder: (context, animation, secondaryAnimation, child) {
                                        return FadeTransition (
                                          opacity: animation,
                                          child: child
                                        );
                                      },
                                    ));
                                  },
                                  child: Text('Sign In',
                                    style: new TextStyle(
                                        fontSize: 20.0,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.white
                                    )
                                  ),
                                  padding: EdgeInsets.fromLTRB(125, 30, 125, 30),
                                  color: Colors.black,
                                  textColor: Colors.white
                              ),
                            ]
                        )
                    ),
                    SizedBox(height: 5)
                  ]
              )
            ]
        )
    );
  }
}