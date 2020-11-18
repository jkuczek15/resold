import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/firebase.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/screens/messages/message.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/models/product.dart';
import 'package:resold/enums/user-message-type.dart';
import 'package:resold/widgets/loading.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:intl/intl.dart';

class InboxPage extends StatefulWidget {
  final CustomerResponse customer;

  InboxPage(customer, {Key key})
      : customer = customer,
        super(key: key);

  @override
  InboxPageState createState() => InboxPageState(customer);
}

class InboxPageState extends State<InboxPage> {
  final CustomerResponse customer;
  List<DocumentSnapshot> messages;
  Stream inboxStream;

  InboxPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          title: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Align(alignment: Alignment.centerLeft, child: Text('Messages', style: new TextStyle(color: Colors.white)))
            ],
          ),
          iconTheme: IconThemeData(
            color: Colors.white, //change your color here
          ),
          backgroundColor: ResoldBlue,
        ),
        body: Column(
          children: [
            Expanded(
                child: StreamBuilder(
                    stream: Firebase.getUserMessagesStream(customer.id),
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
                        return SingleChildScrollView(
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
                                        await Firebase.markInboxMessageRead(item.documentID);

                                        Navigator.push(
                                            context,
                                            MaterialPageRoute(
                                                builder: (context) => MessagePage(customer, toCustomer, product,
                                                    item['chatId'], UserMessageType.values[item['messageType']])));
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
                                                      height: 65,
                                                      width: 57,
                                                      child: Align(
                                                          alignment: Alignment.centerLeft,
                                                          child: CachedNetworkImage(
                                                            placeholder: (context, url) => Container(
                                                              child: Loading(),
                                                              width: 200.0,
                                                              height: 200.0,
                                                              padding: EdgeInsets.all(70.0),
                                                              decoration: BoxDecoration(
                                                                color: Colors.blueGrey,
                                                                borderRadius: BorderRadius.all(
                                                                  Radius.circular(8.0),
                                                                ),
                                                              ),
                                                            ),
                                                            errorWidget: (context, url, error) => Material(
                                                              child: Image.asset(
                                                                'images/img_not_available.jpeg',
                                                                width: 200.0,
                                                                height: 200.0,
                                                                fit: BoxFit.cover,
                                                              ),
                                                              borderRadius: BorderRadius.all(
                                                                Radius.circular(8.0),
                                                              ),
                                                              clipBehavior: Clip.hardEdge,
                                                            ),
                                                            imageUrl: baseProductImagePath + product.thumbnail,
                                                            width: 200.0,
                                                            height: 200.0,
                                                            fit: BoxFit.cover,
                                                          )))
                                                ]),
                                            SizedBox(width: 5),
                                            Column(
                                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                children: [
                                                  SizedBox(height: 4),
                                                  Row(
                                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                    children: [
                                                      SizedBox(width: 5),
                                                      Container(
                                                          width: 160,
                                                          child: Text(product.name,
                                                              overflow: TextOverflow.ellipsis,
                                                              style: item['unread']
                                                                  ? TextStyle(fontWeight: FontWeight.bold)
                                                                  : TextStyle(fontWeight: FontWeight.normal))),
                                                      SizedBox(width: 50),
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
                                                        width: 280,
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
