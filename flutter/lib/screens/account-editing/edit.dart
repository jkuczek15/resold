import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/models/vendor.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/widgets/loading.dart';
import 'package:geolocator/geolocator.dart';
import 'dart:async';
import 'package:geocoder/geocoder.dart';
import 'package:resold/screens/landing/landing.dart';
import 'package:resold/services/magento.dart';

class EditProPage extends StatefulWidget {
  final CustomerResponse customer;

  EditProPage(CustomerResponse customer, {Key key})
      : customer = customer,
        super(key: key);

  @override
  EditProPageState createState() => EditProPageState(customer);
}

class EditProPageState extends State<EditProPage> {
  Future<Vendor> futureVendor;
  final CustomerResponse customer;
  Position currentLocation;

  final firstNameController = TextEditingController();
  final lastNameController = TextEditingController();
  final emailController = TextEditingController();
  final newpasswordController = TextEditingController();
  final newconfirmPasswordController = TextEditingController();
  final passwordController = TextEditingController();
  final formKey = GlobalKey<FormState>();
  Future<List<Address>> futureAddresses;
  Future locationInitialized;

  EditProPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
    futureVendor = Resold.getVendor(customer.vendorId);
    firstNameController.value = TextEditingValue(
      text: customer.firstName,
      selection: TextSelection.fromPosition(
        TextPosition(offset: customer.firstName.length),
      ),
    );
    lastNameController.value = TextEditingValue(
      text: customer.lastName,
      selection: TextSelection.fromPosition(
        TextPosition(offset: customer.lastName.length),
      ),
    );
    emailController.value = TextEditingValue(
      text: customer.email,
      selection: TextSelection.fromPosition(
        TextPosition(offset: customer.email.length),
      ),
    );
    locationInitialized = locationInit();
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      if (this.mounted) {
        setState(() {
          currentLocation = location;
        });
      }
    });
  }

  Future locationInit() async {
    await Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) async {
      if (this.mounted) {
        futureAddresses = Geocoder.local.findAddressesFromCoordinates(new Coordinates(location.latitude, location.longitude));
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<dynamic>>(
        future: Future.wait([futureVendor]),
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            var vendor = snapshot.data[0];

            return Scaffold(
                appBar: AppBar(
                  title: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [Align(alignment: Alignment.centerLeft, child: Text('Edit Profile', style: new TextStyle(color: Colors.white)))],
                  ),
                  iconTheme: IconThemeData(
                    color: Colors.white, // change your color here
                  ),
                  backgroundColor: ResoldBlue,
                  actions: <Widget>[
                    PopupMenuButton<String>(
                      onSelected: handleMenuClick,
                      icon: Icon(Icons.settings),
                      itemBuilder: (BuildContext context) {
                        return {'Manage Addresses', 'Manage Payments', 'Logout', 'Delete Profile'}.map((String choice) {
                          return PopupMenuItem<String>(
                            value: choice,
                            child: Text(choice),
                          );
                        }).toList();
                      },
                    ),
                  ],
                ),
                body: 
                SingleChildScrollView(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    crossAxisAlignment: CrossAxisAlignment.start, 
                    children: [
                      Center(
                        child: Column(
                          children: [
                            Padding(
                              padding: EdgeInsets.fromLTRB(0, 20, 0, 10),
                              child: Container(
                                  height: 115,
                                  width: 115,
                                  child: Padding(
                                    padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                                    child: CircleAvatar(
                                      backgroundImage: vendor.profilePicture != 'null'
                                          ? NetworkImage(baseImagePath + '/' + vendor.profilePicture)
                                          : AssetImage('assets/images/avatar-placeholder.png'),
                                    ),
                                  )),
                            ),
                            InkWell(
                              child: Column(children: [
                                Text('Change Profile Picture',
                                    style:
                                        new TextStyle(fontSize: 16.0, fontFamily: 'Roboto', fontWeight: FontWeight.normal, color: Color(0xff41b8ea))),
                              ]),
                              onTap: () => {
                                // TODO: Get and upload new picture to profile
                              },
                            ),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                                child: TextField(
                                    controller: firstNameController,
                                    decoration: InputDecoration(
                                        helperText: 'First Name',
                                        labelStyle: new TextStyle(
                                          color: ResoldBlue,
                                        ),
                                        enabledBorder: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        ),
                                        focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                        border: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        )),
                                    style: TextStyle(color: Colors.black))),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 20, 50, 10),
                                child: TextField(
                                    controller: lastNameController,
                                    decoration: InputDecoration(
                                        helperText: 'Last Name',
                                        labelStyle: new TextStyle(
                                          color: ResoldBlue,
                                        ),
                                        enabledBorder: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        ),
                                        focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                        border: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        )),
                                    style: TextStyle(color: Colors.black))),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 20, 50, 10),
                                child: TextField(
                                    controller: emailController,
                                    decoration: InputDecoration(
                                        helperText: 'Email Address',
                                        labelStyle: new TextStyle(
                                          color: ResoldBlue,
                                        ),
                                        enabledBorder: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        ),
                                        focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                        border: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        )),
                                    style: TextStyle(color: Colors.black))),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 20, 50, 10),
                                child: TextField(
                                    controller: newpasswordController,
                                    decoration: InputDecoration(
                                        hintText: 'Enter your new password...',
                                        hintStyle: TextStyle(color: ResoldBlue),
                                        helperText: 'New Password',
                                        labelStyle: new TextStyle(
                                          color: ResoldBlue,
                                        ),
                                        enabledBorder: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        ),
                                        focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                        border: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        )),
                                    style: TextStyle(color: Colors.black))),
                            Padding(
                                padding: EdgeInsets.fromLTRB(50, 20, 50, 10),
                                child: TextField(
                                    controller: newconfirmPasswordController,
                                    decoration: InputDecoration(
                                        hintText: 'Confirm your new password...',
                                        hintStyle: TextStyle(color: ResoldBlue),
                                        helperText: 'Confirm New Password',
                                        labelStyle: new TextStyle(
                                          color: ResoldBlue,
                                        ),
                                        enabledBorder: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        ),
                                        focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                        border: UnderlineInputBorder(
                                          borderSide: BorderSide(color: ResoldBlue, width: 1.5),
                                        )),
                                    style: TextStyle(color: Colors.black))),
                            Padding(
                              padding: EdgeInsets.fromLTRB(50, 20, 50, 10),
                              child: ButtonTheme (
                                minWidth: double.infinity,
                                child: RaisedButton(
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                                  onPressed: () async {
                                    //TODO: Submit new data
                                  },
                                  child: Text('Submit', style: new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                                  padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                                  color: Colors.black,
                                  textColor: Colors.white,
                                ),
                              ),
                            ),
                          ],
                        ),
                      )
                    ]
                  )
                )
            );
          } else if (snapshot.hasError) {
            return Text("${snapshot.error}");
          }
          // By default, show a loading spinner.
          return Center(child: Loading());
        });
  }

// TODO: handle cases
  void handleMenuClick(String value) async {
    switch (value) {
      case 'Manage Addresses':
        break;
      case 'Manage Payments':
        break;
      case 'Logout':
      return showDialog<void>(
        context: context,
        barrierDismissible: false,
        builder: (BuildContext context) {
          return AlertDialog(
            title: Text('Logout'),
            content: SingleChildScrollView(
              child: ListBody(
                children: <Widget>[Text("Are you sure you want to logout?")],
              ),
            ),
            actions: <Widget>[
              FlatButton(
                child: Text('Yes',
                style: TextStyle(color: ResoldBlue)),
                onPressed: () {
                  Navigator.push(context, MaterialPageRoute(builder: (context) => LandingPage()));
                },
              ),
              FlatButton(
                child: Text('No', 
                style: TextStyle(color: ResoldBlue),),
                onPressed: () {
                  Navigator.of(context).pop();
                },
              ),
            ],
          );
        },
      );
        break;
      case 'Delete Profile':
      return showDialog<void> (
        context: context,
        barrierDismissible: false,
        builder: (BuildContext context) {
          return AlertDialog (
            title: Text('Delete Profile'),
            content: SingleChildScrollView(
              child: ListBody(
                children: <Widget>[
                  Text("Are you sure you want to delete your profile?",),
                  Form(
                    key: formKey,
                    child: TextFormField(
                      controller: passwordController,
                      obscureText: true,
                      decoration: InputDecoration(
                        labelText: 'Enter Current Password *',
                        labelStyle: TextStyle(color: ResoldBlue),
                        enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                        focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                        border: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                      ),
                      style: TextStyle(color: Colors.black),
                      validator: (value) {
                        if (value.isEmpty || value != customer.password) {
                          return 'Please enter your correct current password.';
                        }
                        return null;
                      },
                    ),
                  ),
                ],
              ),
            ),
            actions: <Widget> [
              FlatButton (
                child: Text('Delete Profile',
                style: TextStyle(
                  color: ResoldBlue),
                  ),
                onPressed: () async {
                  if (formKey.currentState.validate()) {
                    Future<bool> complete = Magento.deleteCustomer(customer.id);
                    if (await complete){ 
                      Navigator.push(context, MaterialPageRoute(builder: (context) => LandingPage()));
                    }
                    else {
                      print("Couldn't complete request");
                      Navigator.of(context).pop();
                      return showDialog<void>(
                        context: context,
                        barrierDismissible: false,
                        builder: (BuildContext context) {
                          return AlertDialog(
                            title: Text("Couldn't complete request"),
                            actions: <Widget>[
                              FlatButton(
                                child: Text('ok',
                                style: TextStyle(
                                  color: ResoldBlue),
                                ),
                                onPressed: () {
                                  Navigator.of(context).pop();
                                }
                              )
                            ]);
                        });
                    }
                  }
                },
              ),
              FlatButton(
                child: Text('Cancel',
                style: TextStyle(
                  color: ResoldBlue),),
                onPressed: () {
                  passwordController.value = TextEditingValue();
                  Navigator.of(context).pop();
                },
              ),
            ],
          );
        },
      );
        break;
    } // end switch on menu click
  }
}
