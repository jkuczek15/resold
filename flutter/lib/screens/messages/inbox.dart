import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:geolocator/geolocator.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/arguments/MessagePageArguments.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/screens/messages/message.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/models/product.dart';
import 'package:resold/widgets/loading.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:intl/intl.dart';

class InboxPage extends StatefulWidget {
  final CustomerResponse customer;
  final Position currentLocation;
  final Function dispatcher;

  InboxPage({CustomerResponse customer, Position currentLocation, Function dispatcher, Key key})
      : customer = customer,
        currentLocation = currentLocation,
        dispatcher = dispatcher,
        super(key: key);

  @override
  InboxPageState createState() => InboxPageState(this.customer, this.currentLocation, this.dispatcher);
}

class InboxPageState extends State<InboxPage> {
  List<DocumentSnapshot> messages;
  Stream inboxStream;
  final CustomerResponse customer;
  final Position currentLocation;
  final Function dispatcher;

  InboxPageState(this.customer, this.currentLocation, this.dispatcher);

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          title: Text('Messages', style: new TextStyle(color: Colors.white)),
          iconTheme: IconThemeData(
            color: Colors.white, //change your color here
          ),
          backgroundColor: ResoldBlue,
        ),
        body: Column(
          children: [
            Expanded(
                child: StreamBuilder(
                    stream: ResoldFirebase.getUserMessagesStream(customer.id),
                    builder: (context, snapshot) {
                      if (!snapshot.hasData) {
                        return Center(child: Loading());
                      } else {
                        List<DocumentSnapshot> messages = new List<DocumentSnapshot>();
                        messages.addAll(snapshot.data[0].documents);
                        messages.addAll(snapshot.data[1].documents);

                        if (messages.length == 0) {
                          return Center(child: Text('You don\'t have any messages'));
                        } // end if no messages

                        messages.sort((DocumentSnapshot a, DocumentSnapshot b) {
                          var dateA = new DateTime.fromMillisecondsSinceEpoch(int.parse(a['lastMessageTimestamp']));
                          var dateB = new DateTime.fromMillisecondsSinceEpoch(int.parse(b['lastMessageTimestamp']));
                          return dateB.compareTo(dateA);
                        });
                        return LiquidPullToRefresh(
                            height: 80,
                            springAnimationDurationInMilliseconds: 500,
                            onRefresh: () async {},
                            showChildOpacityTransition: false,
                            color: ResoldBlue,
                            animSpeedFactor: 5.0,
                            child: ListView.builder(
                                shrinkWrap: true,
                                itemCount: messages.length,
                                padding: EdgeInsets.fromLTRB(2, 10, 2, 10),
                                itemBuilder: (context, index) {
                                  var item = messages[index];
                                  var date =
                                      new DateTime.fromMillisecondsSinceEpoch(int.parse(item['lastMessageTimestamp']));

                                  Product product = Product.fromJson(item['product'], parseId: false);

                                  // check if date is today, if so just use the time
                                  var formattedDate = DateFormat('M/d/yy').format(date);
                                  if (formattedDate == DateFormat('M/d/yy').format(DateTime.now())) {
                                    formattedDate = DateFormat().add_jm().format(date);
                                  } // end if date is today

                                  return InkWell(
                                      onTap: () async {
                                        showDialog(
                                            context: context,
                                            builder: (BuildContext context) {
                                              return Center(child: Loading());
                                            });

                                        // get the to customer details
                                        CustomerResponse toCustomer = await Magento.getCustomerById(item['toId']);

                                        // mark the message as read
                                        await ResoldFirebase.markInboxMessageRead(item.id);

                                        // open message page
                                        Navigator.pushNamed(context, MessagePage.routeName,
                                            arguments: MessagePageArguments(
                                                fromCustomer: customer,
                                                toCustomer: toCustomer,
                                                currentLocation: currentLocation,
                                                product: product,
                                                chatId: item['chatId'],
                                                dispatcher: dispatcher));

                                        Navigator.of(context, rootNavigator: true).pop('dialog');
                                      },
                                      child: Card(
                                          child: ListTile(
                                              title: Container(
                                        child: Row(
                                          mainAxisAlignment: MainAxisAlignment.start,
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Column(
                                                mainAxisAlignment: MainAxisAlignment.start,
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                children: [
                                                  Container(
                                                      child: Align(
                                                          alignment: Alignment.centerLeft,
                                                          child: Padding(
                                                              padding: EdgeInsets.fromLTRB(0, 5, 5, 0),
                                                              child: CircleAvatar(
                                                                backgroundImage: CachedNetworkImageProvider(
                                                                    baseProductImagePath + product.thumbnail),
                                                              ))))
                                                ]),
                                            // SizedBox(width: 5),
                                            Column(
                                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                children: [
                                                  SizedBox(height: 5),
                                                  Row(
                                                    mainAxisAlignment: MainAxisAlignment.end,
                                                    children: [
                                                      SizedBox(width: 5),
                                                      Container(
                                                          child: Text(product.name,
                                                              overflow: TextOverflow.ellipsis,
                                                              style: item['unread']
                                                                  ? TextStyle(fontWeight: FontWeight.bold)
                                                                  : TextStyle(fontWeight: FontWeight.normal))),
                                                      SizedBox(width: 125),
                                                      Container(
                                                          child: Text(formattedDate,
                                                              overflow: TextOverflow.ellipsis,
                                                              style: new TextStyle(color: Colors.grey)))
                                                    ],
                                                  ),
                                                  SizedBox(height: 5),
                                                  Row(children: [
                                                    SizedBox(width: 5),
                                                    Container(
                                                        width: 260,
                                                        height: 25,
                                                        child: Text(item['messagePreview'],
                                                            overflow: TextOverflow.ellipsis,
                                                            style: new TextStyle(color: Colors.grey))),
                                                    SizedBox(height: 4)
                                                  ])
                                                ])
                                          ],
                                        ),
                                      ))));
                                }));
                      } // end if we have query snapshot data
                    }))
          ],
        ));
  } // end function build
}
