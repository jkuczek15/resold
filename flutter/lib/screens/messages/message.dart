import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/enums/delivery-quote-status.dart';
import 'package:resold/helpers/firebase-helper.dart';
import 'package:resold/models/order.dart';
import 'package:resold/screens/order/details.dart';
import 'package:resold/screens/product/view.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/state/actions/cancel-delivery.dart';
import 'package:resold/state/actions/request-delivery.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/firebase/firebase-offer.dart';
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
import 'package:resold/services/postmates.dart';
import 'package:money2/money2.dart';
import 'package:stripe_payment/stripe_payment.dart';
import 'dart:io';

class MessagePage extends StatefulWidget {
  final CustomerResponse fromCustomer;
  final CustomerResponse toCustomer;
  final Position currentLocation;
  final Product product;
  final String chatId;
  final Function dispatcher;

  MessagePage(
      {CustomerResponse fromCustomer,
      CustomerResponse toCustomer,
      Position currentLocation,
      Product product,
      String chatId,
      Function dispatcher,
      Key key})
      : fromCustomer = fromCustomer,
        toCustomer = toCustomer,
        currentLocation = currentLocation,
        product = product,
        chatId = chatId,
        dispatcher = dispatcher,
        super(key: key);

  @override
  MessagePageState createState() =>
      MessagePageState(fromCustomer, toCustomer, currentLocation, product, chatId, dispatcher);
}

class MessagePageState extends State<MessagePage> {
  CustomerResponse fromCustomer;
  final CustomerResponse toCustomer;
  final Position currentLocation;
  final Product product;
  final Function dispatcher;

  var listMessage;
  bool isLoading;
  String chatId;
  String peerAvatar;
  PickedFile pickedImage;
  ImagePicker picker = ImagePicker();
  String imageUrl;
  bool isSeller;
  final offerController = TextEditingController();
  final formKey = GlobalKey<FormState>();
  final int deliveryQuoteRefreshMinutes = 15;

  final TextEditingController textEditingController = TextEditingController();
  final ScrollController listScrollController = ScrollController();
  final FocusNode focusNode = FocusNode();

  MessagePageState(CustomerResponse fromCustomer, CustomerResponse toCustomer, Position currentLocation,
      Product product, String chatId, Function dispatcher)
      : fromCustomer = fromCustomer,
        toCustomer = toCustomer,
        currentLocation = currentLocation,
        product = product,
        chatId = chatId,
        dispatcher = dispatcher;

  @override
  void initState() {
    super.initState();
    peerAvatar = 'assets/images/avatar-placeholder.png';
    isLoading = false;
    isSeller = FirebaseHelper.isSeller(fromCustomer, chatId);
  } // end function initState

  @override
  Widget build(BuildContext context) {
    fromCustomer = widget.fromCustomer;
    return Scaffold(
        appBar: AppBar(
          title: Text(product.name, style: new TextStyle(color: Colors.white)),
          iconTheme: IconThemeData(
            color: Colors.white, //change your color here
          ),
          backgroundColor: ResoldBlue,
          actions: <Widget>[
            PopupMenuButton<String>(
              onSelected: handleMenuClick,
              itemBuilder: (BuildContext context) {
                Set<String> items =
                    product.deliveryId != null ? {'View Details'} : {'View Details', 'Send Offer', 'Request Delivery'};
                return items.map((String choice) {
                  return PopupMenuItem<String>(
                    value: choice,
                    child: Text(choice),
                  );
                }).toList();
              },
            ),
          ],
        ),
        body: getContent());
  } // end function build

  Widget getContent() {
    return Container(
      child: Stack(
        children: [
          Column(children: [
            buildListMessage(),
            buildInput(),
          ]),
          buildLoading()
        ],
      ),
    );
  } // end function getContent

  Future onSendMessage(String content, MessageType type) async {
    if (content.trim() != '') {
      textEditingController.clear();
      await ResoldFirebase.sendProductMessage(chatId, fromCustomer, toCustomer, product, content, type, isSeller);
      await ResoldRest.sendNotificationMessage(
          fromCustomer.token, toCustomer.deviceToken, product.name, content, product.thumbnail,
          chatId: chatId);
      listScrollController.animateTo(0.0, duration: Duration(milliseconds: 300), curve: Curves.easeOut);
    } else {
      Fluttertoast.showToast(msg: 'Nothing to send');
    } // end if content is not null
  } // end function onSendMessage

  Future uploadFile() async {
    String fileName = DateTime.now().millisecondsSinceEpoch.toString();
    Reference reference = FirebaseStorage.instance.ref().child(fileName);
    UploadTask uploadTask = reference.putFile(File(pickedImage.path));
    uploadTask.whenComplete(() {
      uploadTask.snapshot.ref.getDownloadURL().then((downloadUrl) {
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
    });
  } // end function uploadFile

  void handleMenuClick(String value) async {
    switch (value) {
      case 'View Details':
        Navigator.push(
            context,
            MaterialPageRoute(
                builder: (context) => ProductPage(
                    customer: fromCustomer,
                    currentLocation: currentLocation,
                    product: product,
                    dispatcher: dispatcher,
                    fromMessagePage: true)));
        break;
      case 'Request Delivery':
        await requestDelivery();
        break;
      case 'Send Offer':
        await showDialog<void>(
            context: context,
            barrierDismissible: false,
            builder: (BuildContext context) {
              return AlertDialog(
                title: Text('Send an offer'),
                content: SingleChildScrollView(
                  child: ListBody(
                    children: <Widget>[
                      Form(
                        key: formKey,
                        child: TextFormField(
                            controller: offerController,
                            keyboardType: TextInputType.number,
                            decoration: InputDecoration(
                                labelText: 'Enter an offer price *',
                                labelStyle: TextStyle(color: ResoldBlue),
                                enabledBorder:
                                    UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                focusedBorder:
                                    UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                border: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5))),
                            validator: (value) {
                              int offerPrice = int.tryParse(value);
                              if (value.isEmpty || offerPrice < 1) {
                                return 'Please enter a valid offer.';
                              }
                              return null;
                            },
                            style: TextStyle(color: Colors.black)),
                      )
                    ],
                  ),
                ),
                actions: <Widget>[
                  FlatButton(
                    child: Text(
                      'OK',
                      style: TextStyle(color: ResoldBlue),
                    ),
                    onPressed: () async {
                      if (formKey.currentState.validate()) {
                        Navigator.of(context, rootNavigator: true).pop('dialog');

                        // send offer message
                        await ResoldFirebase.sendProductMessage(chatId, fromCustomer, toCustomer, product,
                            fromCustomer.id.toString() + '|' + offerController.text, MessageType.offer, isSeller);

                        // send a notification message
                        await ResoldRest.sendNotificationMessage(fromCustomer.token, toCustomer.deviceToken,
                            product.name, 'Offer received for \$${offerController.text}.', product.thumbnail,
                            chatId: chatId);
                      } // end if valid verification code
                    },
                  ),
                  FlatButton(
                    child: Text(
                      'Cancel',
                      style: TextStyle(color: ResoldBlue),
                    ),
                    onPressed: () {
                      offerController.value = TextEditingValue();
                      Navigator.of(context, rootNavigator: true).pop('dialog');
                    },
                  ),
                ],
              );
            });
        break;
    } // end switch on menu click
  } // end function handleMenuClick

  Future requestDelivery() async {
    // get a Postmates delivery quote
    DeliveryQuoteResponse response = await getDeliveryQuote();

    // prepare message content for delivery request
    String content = response.id +
        '|' +
        response.fee.toString() +
        '|' +
        response.pickup_duration.toString() +
        '|' +
        response.duration.toString();

    // send a Firebase message
    await ResoldFirebase.sendProductMessage(
        chatId, fromCustomer, toCustomer, product, content, MessageType.deliveryQuote, isSeller);

    // request delivery action
    dispatcher(RequestDeliveryAction(
        quote: FirebaseHelper.buildDeliveryQuote(content,
            chatId: chatId,
            fromCustomer: fromCustomer,
            product: product,
            sellerCustomerId: isSeller ? fromCustomer.id : toCustomer.id)));

    // send a notification message
    await ResoldRest.sendNotificationMessage(fromCustomer.token, toCustomer.deviceToken, product.name,
        '${fromCustomer.fullName} has requested a delivery.', product.thumbnail,
        chatId: chatId);
  } // end function requestDelivery

  Future getImage() async {
    pickedImage = await picker.getImage(source: ImageSource.camera);

    if (pickedImage != null) {
      setState(() {
        isLoading = true;
      });
      uploadFile();
    }
  } // end function getImage

  Widget buildLoading() {
    return Positioned(
      child: isLoading ? Loading() : Container(),
    );
  } // end function buildLoading

  Widget buildListMessage() {
    return Flexible(
      child: chatId == ''
          ? Center(child: Loading())
          : StreamBuilder(
              stream: ResoldFirebase.getProductMessagesStream(chatId),
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
  } // end function buildListMessage

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
                keyboardType: TextInputType.multiline,
                maxLines: null,
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
      decoration:
          BoxDecoration(border: Border(top: BorderSide(color: Colors.blueGrey, width: 0.5)), color: Colors.white),
    );
  } // end function buildInput

  Widget buildItem(int index, DocumentSnapshot document) {
    var currency = Currency.create('USD', 2);
    var deliveryQuoteStatus = DeliveryQuoteStatus.none;
    FirebaseDeliveryQuote deliveryQuoteMessage = new FirebaseDeliveryQuote();
    FirebaseOffer offerMessage = new FirebaseOffer();

    if (document['messageType'] == MessageType.deliveryQuote.index) {
      // fetch delivery quote message content
      deliveryQuoteStatus = DeliveryQuoteStatus.values[document['status']];
      deliveryQuoteMessage = FirebaseHelper.buildDeliveryQuote(document['content'],
          chatId: document.id, fromCustomer: fromCustomer, toCustomer: toCustomer, product: product);

      // check if we need to fetch a new delivery quote
      if (deliveryQuoteStatus != DeliveryQuoteStatus.paid && deliveryQuoteStatus != DeliveryQuoteStatus.accepted) {
        DateTime deliveryQuoteSent = DateTime.fromMillisecondsSinceEpoch(int.tryParse(document['timestamp']));
        Duration difference = DateTime.now().difference(deliveryQuoteSent);

        // fetch a new delivery quote
        if (difference.inMinutes >= deliveryQuoteRefreshMinutes) {
          requestDelivery();
        } // end if we need to fetch a new delivery quote
      } // end if delivery quote status not paid
    } else if (document['messageType'] == MessageType.offer.index) {
      offerMessage = FirebaseHelper.buildOffer(document['content']);
    } // end if delivery request

    if (document['idFrom'] == fromCustomer.id) {
      // Right (my message)
      return Row(
        children: <Widget>[
          document['messageType'] == MessageType.text.index
              ?
              // Text
              Container(
                  child: Text(
                    document['content'],
                    style: TextStyle(color: Colors.white),
                  ),
                  padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
                  width: 200.0,
                  decoration: BoxDecoration(color: ResoldBlue, borderRadius: BorderRadius.circular(8.0)),
                  margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                )
              : document['messageType'] == MessageType.canceledDelivery.index
                  ?
                  // Text
                  Container(
                      child: Text(
                        'You have canceled the delivery.',
                        style: TextStyle(color: Colors.white),
                      ),
                      padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
                      width: 200.0,
                      decoration: BoxDecoration(color: ResoldBlue, borderRadius: BorderRadius.circular(8.0)),
                      margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                    )
                  : document['messageType'] == MessageType.image.index
                      ?
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
                              Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                      builder: (context) => FullPhoto(product.name, url: document['content'])));
                            },
                            padding: EdgeInsets.all(0),
                          ),
                          margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                        )
                      : document['messageType'] == MessageType.deliveryQuote.index
                          ?
                          // Delivery Request
                          Container(
                              child: Padding(
                                  padding: EdgeInsets.fromLTRB(7.0, 16.0, 16.0, 0),
                                  child: Column(
                                      mainAxisAlignment: MainAxisAlignment.start,
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Padding(
                                          padding: EdgeInsets.fromLTRB(9.0, 0.0, 0.0, 0),
                                          child: isSeller
                                              ? Text(
                                                  'You have requested a delivery.' +
                                                      '\n\nPickup ETA: ' +
                                                      deliveryQuoteMessage.expectedPickup +
                                                      '\n\nYour profit: ' +
                                                      (deliveryQuoteMessage.fee +
                                                              Money.from(double.tryParse(product.price), currency))
                                                          .toString(),
                                                  style: TextStyle(color: Colors.white))
                                              :
                                              // buyer, delivery was accepted from the seller
                                              !isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.accepted
                                                  ? Text(
                                                      '${toCustomer.fullName} has confirmed your delivery request.' +
                                                          '\n\nDelivery ETA: ' +
                                                          deliveryQuoteMessage.expectedDropoff +
                                                          '\n\nDelivery fee: ' +
                                                          deliveryQuoteMessage.fee.toString() +
                                                          '\n\nTotal: ' +
                                                          (deliveryQuoteMessage.fee +
                                                                  Money.from(double.tryParse(product.price), currency))
                                                              .toString(),
                                                      style: TextStyle(color: Colors.white))
                                                  : !isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.paid
                                                      ? Text(
                                                          'Please wait for a driver to deliver your ${product.name}.\n\nDelivery ETA: ${deliveryQuoteMessage.expectedDropoff.trim()}.\n\nTotal: ${(deliveryQuoteMessage.fee + Money.from(double.tryParse(product.price), currency)).toString()}',
                                                          style: TextStyle(color: Colors.white))
                                                      : Text(
                                                          'You have requested a delivery.' +
                                                              '\n\nDelivery ETA: ' +
                                                              deliveryQuoteMessage.expectedDropoff +
                                                              '\n\nDelivery fee: ' +
                                                              deliveryQuoteMessage.fee.toString() +
                                                              '\n\nTotal: ' +
                                                              (deliveryQuoteMessage.fee +
                                                                      Money.from(
                                                                          double.tryParse(product.price), currency))
                                                                  .toString(),
                                                          style: TextStyle(color: Colors.white)),
                                        ),
                                        Align(
                                            alignment: Alignment.centerLeft,
                                            child: ButtonBar(
                                              alignment: MainAxisAlignment.start,
                                              children: !isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.accepted
                                                  ? [
                                                      FlatButton(
                                                        color: Colors.black,
                                                        textColor: Colors.white,
                                                        onPressed: () async {
                                                          if (isSeller) {
                                                            // user is the seller
                                                            await ResoldFirebase.updateDeliveryQuoteStatus(
                                                                chatId, DeliveryQuoteStatus.accepted);

                                                            // send notification to buyer that delivery has been accepted
                                                            await ResoldRest.sendNotificationMessage(
                                                                fromCustomer.token,
                                                                toCustomer.deviceToken,
                                                                product.name,
                                                                '${fromCustomer.fullName} has accepted your delivery request.',
                                                                product.thumbnail,
                                                                chatId: chatId);
                                                          } else {
                                                            // user is the buyer
                                                            handlePaymentFlow(deliveryQuoteMessage.fee, currency);
                                                          } // end if user is seller
                                                        },
                                                        child: const Text('Accept Delivery'),
                                                      ),
                                                      FlatButton(
                                                        color: Colors.black,
                                                        textColor: Colors.white,
                                                        onPressed: () async {
                                                          // Perform some action
                                                          await ResoldFirebase.deleteProductMessage(
                                                              chatId, document.id);
                                                          dispatcher(CancelDeliveryAction(chatId));
                                                          await ResoldFirebase.sendProductMessage(
                                                              chatId,
                                                              fromCustomer,
                                                              toCustomer,
                                                              product,
                                                              '',
                                                              MessageType.canceledDelivery,
                                                              isSeller);
                                                        },
                                                        child: const Text('Decline Delivery'),
                                                      ),
                                                    ]
                                                  : !isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.paid
                                                      ? []
                                                      : [
                                                          FlatButton(
                                                            color: Colors.black,
                                                            textColor: Colors.white,
                                                            onPressed: () async {
                                                              await ResoldFirebase.deleteProductMessage(
                                                                  chatId, document.id);
                                                              dispatcher(CancelDeliveryAction(chatId));
                                                              await ResoldFirebase.sendProductMessage(
                                                                  chatId,
                                                                  fromCustomer,
                                                                  toCustomer,
                                                                  product,
                                                                  '',
                                                                  MessageType.canceledDelivery,
                                                                  isSeller);
                                                            },
                                                            child: const Text('Cancel Delivery'),
                                                          ),
                                                        ],
                                            ))
                                      ])),
                              width: 260.0,
                              margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                              decoration: BoxDecoration(color: ResoldBlue, borderRadius: BorderRadius.circular(8.0)),
                            )
                          :
                          // Offer
                          Container(
                              child: Padding(
                                  padding: EdgeInsets.fromLTRB(7.0, 16.0, 16.0, 0),
                                  child: Column(
                                      mainAxisAlignment: MainAxisAlignment.start,
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Padding(
                                            padding: EdgeInsets.fromLTRB(9.0, 0.0, 0.0, 0),
                                            child: offerMessage.fromId == fromCustomer.id
                                                ? Text('You have sent an offer for \$${offerMessage.price}.',
                                                    style: TextStyle(color: Colors.white))
                                                : Text('You have received an offer for \$${offerMessage.price}.',
                                                    style: TextStyle(color: Colors.white))),
                                        Align(
                                            alignment: Alignment.centerLeft,
                                            child: ButtonBar(
                                                alignment: MainAxisAlignment.start,
                                                children: offerMessage.fromId == fromCustomer.id
                                                    ? [
                                                        FlatButton(
                                                          color: Colors.black,
                                                          textColor: Colors.white,
                                                          onPressed: () async {
                                                            await ResoldFirebase.deleteProductMessage(
                                                                chatId, document.id);
                                                          },
                                                          child: const Text('Cancel Offer'),
                                                        ),
                                                      ]
                                                    : [
                                                        FlatButton(
                                                          color: Colors.black,
                                                          textColor: Colors.white,
                                                          onPressed: () async {
                                                            // show loading dialog
                                                            showDialog(
                                                                context: context,
                                                                builder: (BuildContext context) {
                                                                  return Center(child: Loading());
                                                                });

                                                            // set the new price
                                                            await ResoldRest.setPrice(
                                                                fromCustomer.token, product.id, offerMessage.price);

                                                            product.price = offerMessage.price.toString();

                                                            // delete the offer message
                                                            await ResoldFirebase.deleteProductMessage(
                                                                chatId, document.id);

                                                            // create a delivery message
                                                            await requestDelivery();

                                                            // close loading dialog
                                                            Navigator.of(context, rootNavigator: true).pop('dialog');
                                                          },
                                                          child: const Text('Accept Offer'),
                                                        ),
                                                        FlatButton(
                                                          color: Colors.black,
                                                          textColor: Colors.white,
                                                          onPressed: () async {
                                                            await ResoldFirebase.deleteProductMessage(
                                                                chatId, document.id);
                                                          },
                                                          child: const Text('Decline Offer'),
                                                        )
                                                      ]))
                                      ])),
                              width: 260.0,
                              margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                              decoration: BoxDecoration(color: ResoldBlue, borderRadius: BorderRadius.circular(8.0)),
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
                        child: Padding(
                            child: FadeInImage(
                                image: NetworkImage(
                                    'https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png'),
                                placeholder: AssetImage('assets/images/avatar-placeholder.png'),
                                width: 35.0,
                                height: 35.0,
                                fit: BoxFit.cover),
                            padding: EdgeInsets.all(10.0)),
                        borderRadius: BorderRadius.all(
                          Radius.circular(18.0),
                        ),
                        clipBehavior: Clip.hardEdge,
                      )
                    : Container(width: 35.0),
                document['messageType'] == MessageType.text.index
                    ? Container(
                        child: Text(
                          document['content'],
                          style: TextStyle(color: Colors.black),
                        ),
                        padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
                        width: 200.0,
                        decoration:
                            BoxDecoration(color: const Color(0xffe1e1e1), borderRadius: BorderRadius.circular(8.0)),
                        margin: EdgeInsets.only(left: 10.0),
                      )
                    : document['messageType'] == MessageType.canceledDelivery.index
                        ?
                        // Text
                        Container(
                            child: Text(
                              '${toCustomer.fullName} has canceled the delivery.',
                              style: TextStyle(color: Colors.white),
                            ),
                            padding: EdgeInsets.fromLTRB(15.0, 10.0, 15.0, 10.0),
                            width: 200.0,
                            decoration: BoxDecoration(color: ResoldBlue, borderRadius: BorderRadius.circular(8.0)),
                            margin: EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                          )
                        : document['messageType'] == MessageType.image.index
                            ? Container(
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
                                    Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                            builder: (context) => FullPhoto(product.name, url: document['content'])));
                                  },
                                  padding: EdgeInsets.all(0),
                                ),
                                margin: EdgeInsets.only(left: 10.0),
                              )
                            : document['messageType'] == MessageType.deliveryQuote.index
                                ?
                                // Delivery Quote
                                Container(
                                    child: Card(
                                        clipBehavior: Clip.antiAlias,
                                        child: Padding(
                                          padding: EdgeInsets.fromLTRB(2.0, 16.0, 16.0, 0),
                                          child: Column(
                                            children: [
                                              Padding(
                                                  padding: EdgeInsets.fromLTRB(12.0, 0.0, 0.0, 0),
                                                  child:
                                                      // seller with open delivery quote
                                                      isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.none
                                                          ? Text(
                                                              'You have received a delivery request.' +
                                                                  '\n\nPickup ETA: ' +
                                                                  deliveryQuoteMessage.expectedPickup +
                                                                  '\n\nYour Profit: ' +
                                                                  (deliveryQuoteMessage.fee + Money.from(double.tryParse(product.price), currency))
                                                                      .toString(),
                                                              style: TextStyle(color: Colors.black.withOpacity(0.6)))
                                                          :
                                                          // seller with accepted delivery quote
                                                          isSeller &&
                                                                  deliveryQuoteStatus == DeliveryQuoteStatus.accepted
                                                              ? Text(
                                                                  'You have accepted the delivery. Please wait for ${toCustomer.fullName} to complete payment.' +
                                                                      '\n\nPickup ETA: ' +
                                                                      deliveryQuoteMessage.expectedPickup +
                                                                      '\n\nYour Profit: ' +
                                                                      (deliveryQuoteMessage.fee + Money.from(double.tryParse(product.price), currency))
                                                                          .toString(),
                                                                  style:
                                                                      TextStyle(color: Colors.black.withOpacity(0.6)))
                                                              :
                                                              // seller with paid delivery quote
                                                              isSeller &&
                                                                      deliveryQuoteStatus == DeliveryQuoteStatus.paid
                                                                  ? Text(
                                                                      '${toCustomer.fullName} has completed payment. Please wait for a driver to pickup your ${product.name}.' +
                                                                          '\n\nPickup ETA: ' +
                                                                          deliveryQuoteMessage.expectedPickup +
                                                                          '\n\nYour Profit: ' +
                                                                          (deliveryQuoteMessage.fee + Money.from(double.tryParse(product.price), currency))
                                                                              .toString(),
                                                                      style: TextStyle(
                                                                          color: Colors.black.withOpacity(0.6)))
                                                                  :
                                                                  // buyer with opened delivery quote
                                                                  !isSeller &&
                                                                          deliveryQuoteStatus ==
                                                                              DeliveryQuoteStatus.none
                                                                      ? Text(
                                                                          'You have received a delivery request.' +
                                                                              '\n\nDelivery ETA: ' +
                                                                              deliveryQuoteMessage.expectedDropoff +
                                                                              '\n\nDelivery fee: ' +
                                                                              deliveryQuoteMessage.fee.toString() +
                                                                              '\n\nTotal: ' +
                                                                              (deliveryQuoteMessage.fee +
                                                                                      Money.from(double.tryParse(product.price), currency))
                                                                                  .toString(),
                                                                          style: TextStyle(color: Colors.black.withOpacity(0.6)))
                                                                      : !isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.paid
                                                                          ? Text('Please wait for a driver to deliver your ${product.name}.' + '\n\nDelivery ETA: ' + deliveryQuoteMessage.expectedDropoff + '\n\nDelivery fee: ' + deliveryQuoteMessage.fee.toString() + '\n\nTotal: ' + (deliveryQuoteMessage.fee + Money.from(double.tryParse(product.price), currency)).toString(), style: TextStyle(color: Colors.black.withOpacity(0.6)))
                                                                          : Text('You have received a delivery request.' + '\n\nDelivery ETA: ' + deliveryQuoteMessage.expectedDropoff + '\n\nDelivery fee: ' + deliveryQuoteMessage.fee.toString() + '\n\nTotal: ' + (deliveryQuoteMessage.fee + Money.from(double.tryParse(product.price), currency)).toString(), style: TextStyle(color: Colors.black.withOpacity(0.6)))),
                                              ButtonBar(
                                                alignment: MainAxisAlignment.start,
                                                children: isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.paid
                                                    ? []
                                                    : isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.accepted
                                                        ? [
                                                            FlatButton(
                                                              color: Colors.black,
                                                              textColor: Colors.white,
                                                              onPressed: () async {
                                                                await ResoldFirebase.deleteProductMessage(
                                                                    chatId, document.id);
                                                                dispatcher(CancelDeliveryAction(chatId));
                                                                await ResoldFirebase.sendProductMessage(
                                                                    chatId,
                                                                    fromCustomer,
                                                                    toCustomer,
                                                                    product,
                                                                    '',
                                                                    MessageType.canceledDelivery,
                                                                    isSeller);
                                                              },
                                                              child: const Text('Cancel Delivery'),
                                                            ),
                                                          ]
                                                        : !isSeller && deliveryQuoteStatus == DeliveryQuoteStatus.paid
                                                            ? []
                                                            : [
                                                                FlatButton(
                                                                  onPressed: () async {
                                                                    if (isSeller) {
                                                                      // user is the seller
                                                                      await ResoldFirebase.updateDeliveryQuoteStatus(
                                                                          chatId, DeliveryQuoteStatus.accepted);

                                                                      // send notification to buyer that delivery has been accepted
                                                                      await ResoldRest.sendNotificationMessage(
                                                                          fromCustomer.token,
                                                                          toCustomer.deviceToken,
                                                                          product.name,
                                                                          '${fromCustomer.fullName} has accepted your delivery request.',
                                                                          product.thumbnail,
                                                                          chatId: chatId);
                                                                    } else {
                                                                      // user is the buyer
                                                                      handlePaymentFlow(
                                                                          deliveryQuoteMessage.fee, currency);
                                                                    } // end if user is seller
                                                                  },
                                                                  child: const Text('Accept Delivery'),
                                                                ),
                                                                FlatButton(
                                                                  onPressed: () async {
                                                                    // Perform some action
                                                                    await ResoldFirebase.deleteProductMessage(
                                                                        chatId, document.id);
                                                                    dispatcher(CancelDeliveryAction(chatId));
                                                                    await ResoldFirebase.sendProductMessage(
                                                                        chatId,
                                                                        fromCustomer,
                                                                        toCustomer,
                                                                        product,
                                                                        '',
                                                                        MessageType.canceledDelivery,
                                                                        isSeller);
                                                                  },
                                                                  child: const Text('Decline Delivery'),
                                                                ),
                                                              ],
                                              )
                                            ],
                                          ),
                                        )),
                                    padding: EdgeInsets.fromLTRB(0, 10.0, 15.0, 0),
                                    width: 275.0,
                                    margin:
                                        EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                                  )
                                :
                                // Offer
                                Container(
                                    child: Padding(
                                        padding: EdgeInsets.fromLTRB(7.0, 16.0, 16.0, 0),
                                        child: Column(
                                            mainAxisAlignment: MainAxisAlignment.start,
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Padding(
                                                  padding: EdgeInsets.fromLTRB(9.0, 0.0, 0.0, 0),
                                                  child: offerMessage.fromId == fromCustomer.id
                                                      ? Text('You have sent an offer for \$${offerMessage.price}.',
                                                          style: TextStyle(color: Colors.white))
                                                      : Text('You have received an offer for \$${offerMessage.price}.',
                                                          style: TextStyle(color: Colors.white))),
                                              Align(
                                                  alignment: Alignment.centerLeft,
                                                  child: ButtonBar(
                                                      alignment: MainAxisAlignment.start,
                                                      children: offerMessage.fromId == fromCustomer.id
                                                          ? [
                                                              FlatButton(
                                                                color: Colors.black,
                                                                textColor: Colors.white,
                                                                onPressed: () async {
                                                                  await ResoldFirebase.deleteProductMessage(
                                                                      chatId, document.id);
                                                                },
                                                                child: const Text('Cancel Offer'),
                                                              ),
                                                            ]
                                                          : [
                                                              FlatButton(
                                                                color: Colors.black,
                                                                textColor: Colors.white,
                                                                onPressed: () async {
                                                                  // show loading dialog
                                                                  showDialog(
                                                                      context: context,
                                                                      builder: (BuildContext context) {
                                                                        return Center(child: Loading());
                                                                      });

                                                                  // set the new price
                                                                  await ResoldRest.setPrice(fromCustomer.token,
                                                                      product.id, offerMessage.price);

                                                                  product.price = offerMessage.price.toString();

                                                                  // delete the offer message
                                                                  await ResoldFirebase.deleteProductMessage(
                                                                      chatId, document.id);

                                                                  // create a delivery message
                                                                  await requestDelivery();

                                                                  // close loading dialog
                                                                  Navigator.of(context, rootNavigator: true)
                                                                      .pop('dialog');
                                                                },
                                                                child: const Text('Accept Offer'),
                                                              ),
                                                              FlatButton(
                                                                color: Colors.black,
                                                                textColor: Colors.white,
                                                                onPressed: () async {
                                                                  await ResoldFirebase.deleteProductMessage(
                                                                      chatId, document.id);
                                                                },
                                                                child: const Text('Decline Offer'),
                                                              )
                                                            ]))
                                            ])),
                                    width: 260.0,
                                    margin:
                                        EdgeInsets.only(bottom: isLastMessageRight(index) ? 20.0 : 10.0, right: 10.0),
                                    decoration:
                                        BoxDecoration(color: ResoldBlue, borderRadius: BorderRadius.circular(8.0)),
                                  )
              ],
            ),
            // Time
            isLastMessageLeft(index)
                ? Container(
                    child: Text(
                      DateFormat('MMM dd, h:mm a')
                          .format(DateTime.fromMillisecondsSinceEpoch(int.parse(document['timestamp']))),
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
    } // end if peer message
  } // end function buildItem

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
        dropoff_deadline_dt: dropoffDeadline.toUtc().toIso8601String()));
  } // end function getDeliveryQuote

  Future<DeliveryResponse> getDelivery({useRobot = false}) async {
    DateTime now = DateTime.now();

    var pickupDeadline = now.add(Duration(minutes: 30));
    var dropoffDeadline = pickupDeadline.add(Duration(hours: 2));

    // include customer ids in Postmates manifest
    int buyerCustomerId;
    int sellerCustomerId;
    if (isSeller) {
      sellerCustomerId = fromCustomer.id;
      buyerCustomerId = toCustomer.id;
    } else {
      buyerCustomerId = fromCustomer.id;
      sellerCustomerId = toCustomer.id;
    } // end if user is the seller

    // create a Postmates delivery
    return await Postmates.createDelivery(
        DeliveryRequest(
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
            manifest_reference:
                product.id.toString() + '|' + buyerCustomerId.toString() + '|' + sellerCustomerId.toString(),
            manifest_items: [new ManifestItem(name: product.name, quantity: 1, size: product.getPostmatesItemSize())]),
        useRobot: useRobot);
  } // end function getDelivery

  handlePaymentFlow(Money fee, Currency currency) async {
    StripePayment.paymentRequestWithNativePay(
      androidPayOptions: AndroidPayPaymentRequest(
        totalPrice:
            (fee + Money.from(double.tryParse(product.price), currency)).toString().replaceAll(new RegExp(r'\$'), ''),
        currencyCode: 'USD',
      ),
      applePayOptions: ApplePayPaymentOptions(
        countryCode: 'US',
        currencyCode: 'USD',
        items: [
          ApplePayItem(
              label: product.name,
              amount: (fee + Money.from(double.tryParse(product.price), currency))
                  .toString()
                  .replaceAll(new RegExp(r'\$'), ''))
        ],
      ),
    ).then((Token token) async {
      // wait for Stripe payment to be complete
      await StripePayment.completeNativePayRequest();

      // show a loading dialog
      showDialog(
          context: context,
          builder: (BuildContext context) {
            return Center(child: Loading());
          });

      // create a Magento order
      int orderId = await Magento.createOrder(fromCustomer.token, fromCustomer.addresses.first, product, token, fee);

      if (orderId != -1) {
        // retreive order details
        Order order = await Magento.getOrderById(orderId);

        // update the message as paid
        await ResoldFirebase.updateDeliveryQuoteStatus(chatId, DeliveryQuoteStatus.paid);

        // create a Postmates delivery
        DeliveryResponse delivery = await getDelivery(useRobot: true);

        // save the delivery ID to the product
        await ResoldRest.setDeliveryId(fromCustomer.token, product.id, delivery.id);
        setState(() => product.deliveryId = delivery.id);
        await ResoldFirebase.setMessageProduct(chatId, product);

        // send notification to buyer that delivery has been accepted
        await ResoldRest.sendNotificationMessage(fromCustomer.token, toCustomer.deviceToken, product.name,
            '${fromCustomer.fullName} has completed payment.', product.thumbnail,
            chatId: chatId);

        // close loading dialog
        Navigator.of(context, rootNavigator: true).pop('dialog');

        // send the user to the order details page
        await Navigator.push(context,
            MaterialPageRoute(builder: (context) => OrderDetails(order: order, product: product, isSeller: false)));
      } else {
        // close loading dialog
        Navigator.of(context, rootNavigator: true).pop('dialog');
        showDialog<void>(
            context: context,
            barrierDismissible: false,
            builder: (BuildContext context) {
              return AlertDialog(title: Text("There was an error while processing your order."), actions: <Widget>[
                FlatButton(
                    child: Text(
                      'OK',
                      style: TextStyle(color: ResoldBlue),
                    ),
                    onPressed: () {
                      Navigator.of(context, rootNavigator: true).pop('dialog');
                      Navigator.of(context, rootNavigator: true).pop('dialog');
                    })
              ]);
            });
      } // end if order successful
    }).catchError((err) {
      showDialog<void>(
          context: context,
          barrierDismissible: false,
          builder: (BuildContext context) {
            return AlertDialog(title: Text("There was an error while processing your order."), actions: <Widget>[
              FlatButton(
                  child: Text(
                    'OK',
                    style: TextStyle(color: ResoldBlue),
                  ),
                  onPressed: () {
                    Navigator.of(context, rootNavigator: true).pop('dialog');
                    Navigator.of(context, rootNavigator: true).pop('dialog');
                  })
            ]);
          });
    });
  } // end function handlePaymentFlow

  bool isLastMessageLeft(int index) {
    if ((index > 0 && listMessage != null && listMessage[index - 1]['idFrom'] == fromCustomer.id) || index == 0) {
      return true;
    } else {
      return false;
    }
  } // end function isLastMessageLeft

  bool isLastMessageRight(int index) {
    if ((index > 0 && listMessage != null && listMessage[index - 1]['idFrom'] != fromCustomer.id) || index == 0) {
      return true;
    } else {
      return false;
    }
  } // end function isLastMessageRight
}
