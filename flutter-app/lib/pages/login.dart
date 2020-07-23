import 'package:flutter/material.dart';
import 'package:resold/pages/home.dart';

class LoginPage extends StatefulWidget {
  LoginPage({Key key}) : super(key: key);

  @override
  LoginPageState createState() => LoginPageState();
}

class LoginPageState extends State<LoginPage> {
  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      child: Scaffold(
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
                              Padding (
                                  padding: EdgeInsets.fromLTRB(65, 30, 65, 30),
                                  child: TextField(
                                    decoration: InputDecoration(
                                      hintText: 'Enter your email...',
                                      hintStyle: TextStyle (
                                        color: Colors.white
                                      ),
                                      labelStyle: new TextStyle(
                                        color: const Color(0xff41b8ea),
                                      )
                                    ),
                                    style: TextStyle (
                                      color: Colors.white
                                    )
                                  )
                                ),
                                Padding (
                                    padding: EdgeInsets.fromLTRB(65, 10, 65, 30),
                                    child: TextField(
                                      decoration: InputDecoration(
                                        hintText: 'Enter your password...',
                                        hintStyle: TextStyle (
                                          color: Colors.white
                                        ),
                                        labelStyle: new TextStyle(
                                          color: const Color(0xff41b8ea),
                                        )
                                      ),
                                      style: TextStyle (
                                        color: Colors.white
                                      )
                                  )
                                ),
                                RaisedButton(
                                  shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadiusDirectional.circular(8)
                                  ),
                                  onPressed: () async {
                                    //after the login REST api call && response code ==200
                                    Navigator.pop(context);
                                    Navigator.pushReplacement(context, PageRouteBuilder(
                                      pageBuilder: (context, animation, secondaryAnimation) => Home(),
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
                                  padding: EdgeInsets.fromLTRB(105, 30, 105, 30),
                                  color: Colors.black,
                                  textColor: Colors.white,
                                ),
                                SizedBox(height: 10)
                              ]
                          )
                      ),
                      SizedBox(height: 5)
                    ]
                )
              ]
          )
      ),
      onWillPop: () async {
        Navigator.pop(context);
        return false;
      }
    );
  }
}
