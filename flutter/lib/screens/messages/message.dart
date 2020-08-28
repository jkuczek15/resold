import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:resold/services/firebase.dart';
import 'package:resold/models/product.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/widgets/full-photo.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:intl/intl.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:image_picker/image_picker.dart';
import 'package:firebase_storage/firebase_storage.dart';
import 'package:resold/widgets/loading.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:resold/enums/message-type.dart';
import 'package:resold/enums/user-message-type.dart';
import 'dart:io';

class MessagePage extends StatefulWidget {
  final Product product;
  final CustomerResponse customer;
  final int toId;
  final String chatId;
  final UserMessageType type;

  MessagePage(customer, product, toId, chatId, type, {Key key}) : customer = customer, product = product, toId = toId, chatId = chatId, type = type, super(key: key);

  @override
  MessagePageState createState() => MessagePageState(customer, product, toId, chatId, type);
}

class MessagePageState extends State<MessagePage> {

  final CustomerResponse customer;
  final Product product;
  final UserMessageType type;
  int toId;

  var listMessage;
  bool isLoading;
  String chatId;
  String peerAvatar;
  File imageFile;
  String imageUrl;

  final TextEditingController textEditingController = TextEditingController();
  final ScrollController listScrollController = ScrollController();
  final FocusNode focusNode = FocusNode();

  MessagePageState(CustomerResponse customer, Product product, int toId, String chatId, UserMessageType type)
      : customer = customer, product = product, toId = toId, chatId = chatId, type = type;

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
      await Firebase.sendProductMessage(chatId, customer.id, toId, product, content, type);
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
      child: isLoading ? const Loading() : Container(),
    );
  }

  Widget buildListMessage() {
    return Flexible(
      child: chatId == ''
          ? Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)))
          : StreamBuilder(
        stream: Firebase.getProductMessagesStream(chatId),
        builder: (context, snapshot) {
          if (!snapshot.hasData) {
            return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
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
    if (document['idFrom'] == customer.id) {
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
                    child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)),
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
              'You have sent a request to purchase this item.',
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
                          child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)),
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
                            'Someone has requested to purchase your item.',
                            style: TextStyle(color: Colors.black.withOpacity(0.6))
                          ),
                        ),
                        ButtonBar(
                          alignment: MainAxisAlignment.start,
                          children: [
                            FlatButton(
                              textColor: const Color(0xff41b8ea),
                              onPressed: () {
                                // Perform some action
                              },
                              child: const Text('Schedule Pickup'),
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

  bool isLastMessageLeft(int index) {
    if ((index > 0 && listMessage != null && listMessage[index - 1]['idFrom'] == customer.id) || index == 0) {
      return true;
    } else {
      return false;
    }
  }

  bool isLastMessageRight(int index) {
    if ((index > 0 && listMessage != null && listMessage[index - 1]['idFrom'] != customer.id) || index == 0) {
      return true;
    } else {
      return false;
    }
  }

}
