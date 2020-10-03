import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:resold/services/firebase.dart';
import 'package:resold/models/product.dart';
import 'package:resold/view-models/request/postmates/delivery-quote-request.dart';
import 'package:resold/view-models/request/postmates/delivery-request.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/view-models/response/postmates/delivery-quote-response.dart';
import 'package:resold/view-models/response/postmates/delivery-response.dart';
import 'package:resold/widgets/image/full-photo.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:intl/intl.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:image_picker/image_picker.dart';
import 'package:firebase_storage/firebase_storage.dart';
import 'package:resold/widgets/loading.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:resold/enums/message-type.dart';
import 'package:resold/enums/user-message-type.dart';
import 'package:resold/services/postmates.dart';
import 'package:money2/money2.dart';
import 'package:stripe_payment/stripe_payment.dart';
import 'dart:io';

class MessagePage extends StatefulWidget {
  final Product product;
  final CustomerResponse fromCustomer;
  final CustomerResponse toCustomer;
  final String chatId;
  final UserMessageType type;

  MessagePage(fromCustomer, toCustomer, product, chatId, type, {Key key}) : fromCustomer = fromCustomer, toCustomer = toCustomer,
        product = product, chatId = chatId, type = type, super(key: key);

  @override
  MessagePageState createState() => MessagePageState(fromCustomer, toCustomer, product, chatId, type);
}

class MessagePageState extends State<MessagePage> {

  final CustomerResponse fromCustomer;
  final CustomerResponse toCustomer;
  final Product product;
  final UserMessageType type;

  var listMessage;
  bool isLoading;
  String chatId;
  String peerAvatar;
  File imageFile;
  String imageUrl;

  final TextEditingController textEditingController = TextEditingController();
  final ScrollController listScrollController = ScrollController();
  final FocusNode focusNode = FocusNode();

  MessagePageState(CustomerResponse fromCustomer, CustomerResponse toCustomer, Product product, String chatId, UserMessageType type)
      : fromCustomer = fromCustomer, toCustomer = toCustomer, product = product, chatId = chatId, type = type;

  @override
  void initState() {
    super.initState();
    peerAvatar = 'assets/images/avatar-placeholder.png';
    isLoading = false;
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
                child: Container (
                  width: 260,
                  child: Text(product.name, overflow: TextOverflow.ellipsis, style: new TextStyle(color: Colors.white))
                )
              )
            ],
          ),
          iconTheme: IconThemeData(
            color: Colors.white, //change your color here
          ),
          backgroundColor: const Color(0xff41b8ea),
        ),
        body: getContent()
    );
  }

  Widget getContent() {
    return Container (
      child: Stack (
        children: [
          Column (
            children: [
              buildListMessage(),
              buildInput(),
            ]
          ),
          buildLoading()
        ],
      ),
    );
  }

  Future onSendMessage(String content, MessageType type) async {
    if (content.trim() != '') {
      textEditingController.clear();
      await Firebase.sendProductMessage(chatId, fromCustomer.id, toCustomer.id, product, content, type);
      listScrollController.animateTo(0.0, duration: Duration(milliseconds: 300), curve: Curves.easeOut);
    } else {
      Fluttertoast.showToast(msg: 'Nothing to send');
    }
  }

  Future uploadFile() async {
    String fileName = DateTime.now().millisecondsSinceEpoch.toString();
    StorageReference reference = FirebaseStorage.instance.ref().child(fileName);
    StorageUploadTask uploadTask = reference.putFile(imageFile);
    StorageTaskSnapshot storageTaskSnapshot = await uploadTask.onComplete;
    storageTaskSnapshot.ref.getDownloadURL().then((downloadUrl) {
      imageUrl = downloadUrl;
      setState(() {
        isLoading = false;
        onSendMessage(imageUrl, MessageType.image);
      });
    }, onError: (err) {
      setState(() {
        isLoading = false;
      });
      Fluttertoast.showToast(msg: 'This file is not an image');
    });
  }

  Future getImage() async {
    imageFile = await ImagePicker.pickImage(source: ImageSource.gallery);

    if (imageFile != null) {
      setState(() {
        isLoading = true;
      });
      uploadFile();
    }
  }

  Widget buildLoading() {
    return Positioned(
      child: isLoading ? Loading() : Container(),
    );
  }

  Widget buildListMessage() {
    return Flexible(
      child: chatId == ''
          ? Center(child: Loading())
          : StreamBuilder(
        stream: Firebase.getProductMessagesStream(chatId),
        builder: (context, snapshot) {
          if (!snapshot.hasData) {
            return Center(child: Loading());
          } else {
            listMessage = snapshot.data.documents;
            return ListView.builder(
              padding: EdgeInsets.all(10.0),
              itemBuilder: (context, index) => buildItem(index, listMessage[index]),
              itemCount: listMessage.length,
              reverse: true,
              controller: listScrollController,
            );
          }
        },
      ),
    );
  }

  Widget buildInput() {
    return Container(
      child: Row(
        children: [
          // Button send image
          Material(
            child: Container(
              margin: EdgeInsets.symmetric(horizontal: 1.0),
              child: IconButton(
                icon: Icon(Icons.image),
                onPressed: getImage,
                color: Colors.black,
              ),
            ),
            color: Colors.white,
          ),
          // Edit text
          Flexible(
            child: Container(
              child: TextField(
                style: TextStyle(color: Colors.black, fontSize: 15.0),
                controller: textEditingController,
                decoration: InputDecoration.collapsed(
                  hintText: 'Type your message...',
                  hintStyle: TextStyle(color: Colors.blueGrey),
                ),
                focusNode: focusNode,
              ),
            ),
          ),

          // Button send message
          Material(
            child: Container(
              margin: EdgeInsets.symmetric(horizontal: 8.0),
              child: IconButton(
                icon: Icon(Icons.send),
                onPressed: () => onSendMessage(textEditingController.text, MessageType.text),
                color: Colors.black,
              ),
            ),
            color: Colors.white,
          ),
        ],
      ),
      width: double.infinity,
      height: 50.0,
      decoration: BoxDecoration(border: Border(top: BorderSide(color: Colors.blueGrey, width: 0.5)), color: Colors.white),
    );
  }

  Widget buildItem(int index, DocumentSnapshot document) {

    var currency = Currency.create('USD', 2);
    var quoteId, fee, expectedPickup, expectedDropoff = '';
    if(document['type'] == MessageType.deliveryRequest.index) {
      var content = document['content'].split('|');
      quoteId = content[0];
      fee = Money.fromInt(int.tryParse(content[1]), currency);
      expectedPickup = DateFormat('h:mm a on MM/dd/yyyy.').format(DateTime.tryParse(DateTime.now().add(Duration(minutes: int.tryParse(content[2]))).toString()))
          .replaceAll(new RegExp(r'on ' + DateFormat('MM/dd/yyyy').format(DateTime.now()) + '.'), '');
      expectedDropoff = DateFormat('h:mm a on MM/dd/yyyy.').format(DateTime.tryParse(DateTime.now().add(Duration(minutes: int.tryParse(content[3]))).toString()))
          .replaceAll(new RegExp(r'on ' + DateFormat('MM/dd/yyyy').format(DateTime.now()) + '.'), '');
    }// end if delivery request

    if (document['idFrom'] == fromCustomer.id) {
      // Right (my message)
      return Row(
        children: <Widget>[
          document['type'] == MessageType.text.index ?
          // Text
          Container(
            child: Text(
              document['content'],
              style: TextStyle(color: Colors.white),
            ),
            padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
            width: 200.0,
            decoration: BoxDecoration(color: const Color(0xff41b8ea), borderRadius: BorderRadius.circular(8.0)),
            margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
          )
            : document['type'] == MessageType.image.index ?
            // Image
            Container(
            child: FlatButton(
              child: Material(
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
                  imageUrl: document['content'],
                  width: 200.0,
                  height: 200.0,
                  fit: BoxFit.cover,
                ),
                borderRadius: BorderRadius.all(Radius.circular(8.0)),
                clipBehavior: Clip.hardEdge,
              ),
              onPressed: () {
                Navigator.push(context, MaterialPageRoute(builder: (context) => FullPhoto(product.name, url: document['content'])));
              },
              padding: EdgeInsets.all(0),
            ),
            margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
          )
          : document['type'] == MessageType.purchaseRequest.index ?
          // Purchase Request
          Container(
            child: Text(
              'You have sent a request to purchase this item from ${toCustomer.fullName}.',
              style: TextStyle(color: Colors.white60),
            ),
            padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
            width: 200.0,
            decoration: BoxDecoration(color: const Color(0xff41b8ea), borderRadius: BorderRadius.circular(8.0)),
            margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
          )
          : document['type'] == MessageType.deliveryRequest.index ?
          // Delivery Request
          Container(
            child: Text(
              'You have sent a delivery request to ${toCustomer.fullName}.'
                  + '\n\nPickup ETA: ' + expectedPickup,
              style: TextStyle(color: Colors.white60),
            ),
            padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
            width: 200.0,
            decoration: BoxDecoration(color: const Color(0xff41b8ea), borderRadius: BorderRadius.circular(8.0)),
            margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
          )
          :
          // Offer
          Container(
            child: Text(
              'Offer received for ${product.name}.',
              style: TextStyle(color: Colors.grey),
            ),
            padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
            width: 200.0,
            decoration: BoxDecoration(color: const Color(0xff41b8ea), borderRadius: BorderRadius.circular(8.0)),
            margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
          )
        ],
        mainAxisAlignment: MainAxisAlignment.end,
      );
    } else {
      // Left (peer message)
      return Container(
        child: Column(
          children: <Widget>[
            Row(
              children: <Widget>[
                isLastMessageLeft(index)
                    ? Material(
                  child: Padding (
                    child: FadeInImage(
                        image: NetworkImage('https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png'),
                        placeholder: AssetImage('assets/images/avatar-placeholder.png'),
                        width: 35.0,
                        height: 35.0,
                        fit: BoxFit.cover
                      ),
                      padding: EdgeInsets.all(10.0)
                  ),
                  borderRadius: BorderRadius.all(
                    Radius.circular(18.0),
                  ),
                  clipBehavior: Clip.hardEdge,
                )
                  : Container(width: 35.0),
                document['type'] == MessageType.text.index ?
                Container(
                  child: Text(
                    document['content'],
                    style: TextStyle(color: Colors.black),
                  ),
                  padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
                  width: 200.0,
                  decoration: BoxDecoration(color: const Color(0xffe1e1e1), borderRadius: BorderRadius.circular(8.0)),
                  margin: EdgeInsets.only(left: 10.0),
                )
                : document['type'] == MessageType.image.index ?
                  Container(
                  child: FlatButton(
                    child: Material(
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
                        imageUrl: document['content'],
                        width: 200.0,
                        height: 200.0,
                        fit: BoxFit.cover,
                      ),
                      borderRadius: BorderRadius.all(Radius.circular(8.0)),
                      clipBehavior: Clip.hardEdge,
                    ),
                    onPressed: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => FullPhoto(product.name, url: document['content'])));
                    },
                    padding: EdgeInsets.all(0),
                  ),
                  margin: EdgeInsets.only(left: 10.0),
                )
                : document['type'] == MessageType.purchaseRequest.index ?
                // Purchase Request
                Container(
                  child:
                  Card(
                    clipBehavior: Clip.antiAlias,
                    child: Column(
                      children: [
                        Padding(
                          padding: EdgeInsets.fromLTRB(16.0, 16.0, 16.0, 0),
                          child: Text(
                            '${toCustomer.fullName} has sent you a request to purchase your item.',
                            style: TextStyle(color: Colors.black.withOpacity(0.6))
                          ),
                        ),
                        ButtonBar(
                          alignment: MainAxisAlignment.start,
                          children: [
                            FlatButton(
                              textColor: const Color(0xff41b8ea),
                              onPressed: () async {
                                // get a Postmates delivery quote
                                DeliveryQuoteResponse response = await getDeliveryQuote();

                                // prepare message content for delivery request
                                String content = response.id + '|' + response.fee.toString() + '|' + response.pickup_duration.toString() + '|' + response.duration.toString();

                                // send a Firebase message
                                await Firebase.sendProductMessage(chatId, fromCustomer.id, toCustomer.id, product, content, MessageType.deliveryRequest);
                              },
                              child: const Text('Request Pickup'),
                            ),
                            FlatButton(
                              textColor: const Color(0xff41b8ea),
                              onPressed: () {
                                // Perform some action
                              },
                              child: const Text('Request Payment'),
                            ),
                          ],
                        )
                      ],
                    ),
                  ),
                  padding: EdgeInsets.fromLTRB(0, 10.0, 15.0, 0),
                  width: 275.0,
                  margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                )
                : document['type'] == MessageType.deliveryRequest.index ?
                // Purchase Request
                Container(
                  child:
                  Card(
                    clipBehavior: Clip.antiAlias,
                    child: Column(
                      children: [
                        Padding(
                          padding: EdgeInsets.fromLTRB(16.0, 16.0, 16.0, 0),
                          child: Text(
                            '${toCustomer.fullName} has sent you a delivery request.'
                                + '\n\n${product.name}'
                                + '\n\nDelivery ETA: ' + expectedDropoff
                                + '\n\nDelivery fee: ' + fee.toString()
                                + '\n\nTotal: ' + (fee
                                + Money.from(double.tryParse(product.price), currency)).toString(),
                            style: TextStyle(color: Colors.black.withOpacity(0.6))
                          ),
                        ),
                        ButtonBar(
                          alignment: MainAxisAlignment.start,
                          children: [
                            FlatButton(
                              textColor: const Color(0xff41b8ea),
                              onPressed: () async {
                                StripePayment.paymentRequestWithNativePay(
                                  androidPayOptions: AndroidPayPaymentRequest(
                                    totalPrice: (fee + Money.from(double.tryParse(product.price), currency)).toString().replaceAll(new RegExp(r'\$'), ''),
                                    currencyCode: 'USD',
                                  ),
                                  applePayOptions: ApplePayPaymentOptions(
                                    countryCode: 'US',
                                    currencyCode: 'USD',
                                    items: [
                                      ApplePayItem(
                                        label: product.name,
                                        amount: (fee + Money.from(double.tryParse(product.price), currency)).toString().replaceAll(new RegExp(r'\$'), '')
                                      )
                                    ],
                                  ),
                                ).then((Token token) async {
                                  // todo: create a card charge and a delivery
                                  DeliveryResponse response = await getDelivery();

                                  print(token);
                                }).catchError((err) {
                                  print(err);
                                });
                              },
                              child: const Text('Accept Delivery'),
                            ),
                            FlatButton(
                              textColor: const Color(0xff41b8ea),
                              onPressed: () async {
                                await Firebase.deleteProductMessage(chatId, document.documentID);
                              },
                              child: const Text('Decline Delivery'),
                            ),
                          ],
                        )
                      ],
                    ),
                  ),
                  padding: EdgeInsets.fromLTRB(0, 10.0, 15.0, 0),
                  width: 275.0,
                  margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                )
                :
                // Offer
                Container(
                  child: Text(
                    'Offer received for ${product.name}.',
                    style: TextStyle(color: Colors.grey),
                  ),
                  padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
                  width: 200.0,
                  decoration: BoxDecoration(color: const Color(0xff41b8ea), borderRadius: BorderRadius.circular(8.0)),
                  margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                )
              ],
            ),
            // Time
            isLastMessageLeft(index)
                ? Container(
              child: Text(
                DateFormat('MMM dd, h:mm a').format(DateTime.fromMillisecondsSinceEpoch(int.parse(document['timestamp']))),
                style: TextStyle(color: Colors.grey, fontSize: 12.0, fontStyle: FontStyle.italic),
              ),
              margin: EdgeInsets.only(left: 50.0, top: 5.0, bottom: 5.0),
            )
                : Container()
          ],
          crossAxisAlignment: CrossAxisAlignment.start,
        ),
        margin: EdgeInsets.only(bottom: 10.0),
      );
    }
  }

  Future<DeliveryQuoteResponse> getDeliveryQuote() async {
    DateTime now = DateTime.now();

    var pickupDeadline = now.add(Duration(minutes: 30));
    var dropoffDeadline = pickupDeadline.add(Duration(hours: 2));

    // create a Postmates delivery quote
    return await Postmates.createDeliveryQuote(DeliveryQuoteRequest(
        pickup_address: fromCustomer.addresses.first.toString(),
        pickup_ready_dt: now.toUtc().toIso8601String(),
        pickup_deadline_dt: pickupDeadline.toUtc().toIso8601String(),
        dropoff_address: toCustomer.addresses.first.toString(),
        dropoff_ready_dt: now.toUtc().toIso8601String(),
        dropoff_deadline_dt: dropoffDeadline.toUtc().toIso8601String()
    ));
  }

  Future<DeliveryResponse> getDelivery() async {
    DateTime now = DateTime.now();

    var pickupDeadline = now.add(Duration(minutes: 30));
    var dropoffDeadline = pickupDeadline.add(Duration(hours: 2));

    // create a Postmates delivery
    return await Postmates.createDelivery(DeliveryRequest(
        pickup_name: toCustomer.fullName,
        pickup_phone_number: toCustomer.addresses.first.telephone,
        pickup_address: toCustomer.addresses.first.toString(),
        pickup_ready_dt: now.toUtc().toIso8601String(),
        pickup_deadline_dt: pickupDeadline.toUtc().toIso8601String(),
        dropoff_name: fromCustomer.fullName,
        dropoff_phone_number: fromCustomer.addresses.first.telephone,
        dropoff_address: fromCustomer.addresses.first.toString(),
        dropoff_ready_dt: now.toUtc().toIso8601String(),
        dropoff_deadline_dt: dropoffDeadline.toUtc().toIso8601String(),
        manifest: product.name,
        manifest_items: [new ManifestItem(
          name: product.name,
          quantity: 1,
          size: product.getPostmatesItemSize()
        )]
    ));
  }

  bool isLastMessageLeft(int index) {
    if ((index > 0 && listMessage != null && listMessage[index - 1]['idFrom'] == fromCustomer.id) || index == 0) {
      return true;
    } else {
      return false;
    }
  }

  bool isLastMessageRight(int index) {
    if ((index > 0 && listMessage != null && listMessage[index - 1]['idFrom'] != fromCustomer.id) || index == 0) {
      return true;
    } else {
      return false;
    }
  }

}
