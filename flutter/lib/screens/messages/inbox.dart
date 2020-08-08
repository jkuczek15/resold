import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/services/firebase.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';

class InboxPage extends StatefulWidget {
  final CustomerResponse customer;

  InboxPage(customer, {Key key}) : customer = customer, super(key: key);

  @override
  InboxPageState createState() => InboxPageState(customer);
}

class InboxPageState extends State<InboxPage> {

  final CustomerResponse customer;

  InboxPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold (
      appBar: AppBar(
        title: Row (
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Align (
                alignment: Alignment.centerLeft,
                child: Text('Messages', style: new TextStyle(color: Colors.white))
            ),
            Align (
                alignment: Alignment.centerRight,
                child: InkWell (
                  child: Icon(MdiIcons.dotsVertical, color: Colors.white),
                  onTap: () {
                  },
                )
            )
          ],
        ),
        iconTheme: IconThemeData(
          color: Colors.white, //change your color here
        ),
        backgroundColor: const Color(0xff41b8ea),
      ),
      body: Column (
        children: [
          Expanded (
              child: StreamBuilder (
                  stream: Firebase.getUserMessagesStream(),
                  builder: (context, snapshot) {
                    if (!snapshot.hasData) {
                      return Center(
                          child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea))
                      );
                    } else {
                      return ListView.builder(
                          shrinkWrap: true,
                          itemCount: snapshot.data.documents.length,
                          padding: EdgeInsets.all(10.0),
                          itemBuilder: (context, index) {
                            var item = snapshot.data.documents[index];
                            return ListTile(
                                title: Container(
                                  height: 50,
                                  child: Row(
                                    children: [
                                      Text('testing')
                                    ],
                                  ),
                                )
                            );
                          }
                      );
                    }
                  }
              )
          )
        ],
      )
    );
  }
}
