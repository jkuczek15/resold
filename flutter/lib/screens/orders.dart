import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';

class OrdersPage extends StatefulWidget {
  OrdersPage({Key key}) : super(key: key);

  @override
  AccountPageState createState() => AccountPageState();
}

class AccountPageState extends State<OrdersPage> {

  @override
  Widget build(BuildContext context) {
    return Padding (
      padding: EdgeInsets.all(20),
      child:  ButtonTheme (
          minWidth: 340.0,
          height: 70.0,
          child: RaisedButton(
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadiusDirectional.circular(8)
            ),
            onPressed: () async {
              // show a loading indicator
              showDialog(
                context: context,
                builder: (BuildContext context) {
                  return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                }
              );
              Navigator.of(context, rootNavigator: true).pop('dialog');
            },
            child: Text('Orders',
              style: new TextStyle(
                fontSize: 20.0,
                fontWeight: FontWeight.bold,
                color: Colors.white
              )
            ),
            color: Colors.black,
            textColor: Colors.white,
          )
      )
    );
  }
}
