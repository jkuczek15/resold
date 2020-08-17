import 'dart:async';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:resold/enums/message-type.dart';

/*
* Resold Firebase API service - Firebase specific API client
* This service is used to make real-time requests
*/
class Firebase {

  /*
  * createUser - Create a Firebase user given customer data
  * response - Customer response data
  */
  static Future createUser(CustomerResponse response) async {
    // check if we have a firebase user
    QuerySnapshot result = await Firestore.instance.collection('users').where('id', isEqualTo: response.id).getDocuments();
    List <DocumentSnapshot> documents = result.documents;
    if (documents.length == 0) {
      // create a new user
      Firestore.instance.collection('users').document(response.id.toString()).setData({
        'id': response.id,
        'email': response.email,
        'nickname': response.firstName + ' ' + response.lastName,
        'vendorId': response.vendorId
      });
    }// end if we need to create a new user
  }// end function createUser

  /*
  * createUserInboxMessage - Creates an inbox chat message for a customer for storing a chat
  * fromId - Customer from ID
  * toId - Customer to ID
  * chatId - ID of the chat where messages will be stored
  * type - Type of the message (buyer or seller)
  * content - Content of the message for message preview
  * product - Product that message group is related to.
  */
  static Future createUserInboxMessage(int fromId, int toId, String chatId, MessageType type, String content, Product product) async {
    // get existing message collection
    var userMessageId = fromId.toString() + '-' + toId.toString() + '-' + product.id.toString();
    QuerySnapshot result = await Firestore.instance.collection('inbox_messages').where('id', isEqualTo: userMessageId).getDocuments();
    List <DocumentSnapshot> documents = result.documents;

    // set up params to store for user inbox message
    var now = DateTime.now().millisecondsSinceEpoch.toString();

    // setup message preview and trim if necessary
    var messagePreview = content;
    if(content.length > 50) {
      messagePreview = content.substring(0, 50);
    }// end if content length > 50

    if (documents.length == 0) {
      // create a inbox message
      Firestore.instance.collection('inbox_messages').document(userMessageId).setData({
        'fromId': fromId,
        'toId': toId,
        'chatId': chatId,
        'product': product.toJson(),
        'type': type.index,
        'messagePreview': messagePreview,
        'lastMessageTimestamp': now
      });
    } else {
      // update the last message timestamp and preview
      documents[0].data['messagePreview'] = messagePreview;
      documents[0].data['lastMessageTimestamp'] = now;
      Firestore.instance.collection('inbox_messages').document(userMessageId).setData(documents[0].data);
    }// end if we need to create a new inbox message
  }// end function createUserInboxMessage

  /*
  * sendProductMessage - Send a product message from one user to another
  * fromId - Customer from ID
  * toId - Customer to ID
  * product - Product that message group is related to.
  * content - Content of the message for message preview
  * type - Type of the message (buyer or seller)
  */
  static Future sendProductMessage(int fromId, int toId, Product product, String content, int type) async {

    var chatId = fromId.toString() + '-' + toId.toString() + '-' + product.id.toString();

    await createUserInboxMessage(fromId, toId, chatId, MessageType.buyer, content, product);
    await createUserInboxMessage(toId, fromId, chatId, MessageType.seller, content, product);

    var documentReference = Firestore.instance
        .collection('messages')
        .document(chatId)
        .collection(chatId)
        .document(DateTime.now().millisecondsSinceEpoch.toString());

    Firestore.instance.runTransaction((transaction) async {
      await transaction.set(
        documentReference,
        {
          'idFrom': fromId,
          'idTo': toId,
          'timestamp': DateTime.now().millisecondsSinceEpoch.toString(),
          'content': content,
          'type': type
        },
      );
    });
  }// end function sendProductMessage

  /*
  * getUserMessagesStream - Returns a real time messages stream for the inbox
  * customerId - Customer from ID for messages
  */
  static Stream getUserMessagesStream(int customerId) {
    return Firestore.instance.collection('inbox_messages')
        .where(FieldPath.documentId, isGreaterThanOrEqualTo: customerId.toString())
        .where(FieldPath.documentId, isLessThan: (customerId+1).toString())
        .orderBy(FieldPath.documentId)
        .limit(20)
        .snapshots();
  }// end function getUserMessagesStream

  /*
  * getProductMessagesStream - Returns a real time messages stream for a certain chat group
  * chatId - Chat ID for product messages
  */
  static Stream getProductMessagesStream(String chatId) {
    return Firestore.instance
        .collection('messages')
        .document(chatId)
        .collection(chatId)
        .orderBy('timestamp', descending: true)
        .limit(20)
        .snapshots();
  }// end function getProductMessagesStream

  /*
  * configure - Configure Firebase settings
  */
  static Future configure() async {
    await Firestore.instance.settings(persistenceEnabled: false);
  }// end function configure

}// end class Firebase
