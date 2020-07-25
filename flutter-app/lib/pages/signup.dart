import 'package:flutter/material.dart';
import 'package:resold/pages/home.dart';
import 'package:geolocator/geolocator.dart';
import 'package:geocoder/geocoder.dart';

class SignUpPage extends StatefulWidget {
  SignUpPage({Key key}) : super(key: key);

  @override
  SignUpPageState createState() => SignUpPageState();
}

class SignUpPageState extends State<SignUpPage> {

  Address address;

  @override
  void initState() {
    super.initState();

    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      setState(() async {
        var addresses = await Geocoder.local.findAddressesFromCoordinates(new Coordinates(
            location.latitude, location.longitude
          )
        );
        address = addresses.first;
      });
    });
  }

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
                                  child: Text('Sign Up',
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