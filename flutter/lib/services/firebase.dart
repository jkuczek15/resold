import 'dart:async';
import 'package:resold/enums/delivery-quote-status.dart';
import 'package:resold/enums/message-type.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:resold/enums/user-message-type.dart';
import 'package:resold/helpers/firebase-helper.dart';

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
    QuerySnapshot result = await Firestore.instance
        .collection('users')
        .where('id', isEqualTo: response.id)
        .getDocuments();
    List<DocumentSnapshot> documents = result.documents;
    if (documents.length == 0) {
      // create a new user
      Firestore.instance
          .collection('users')
          .document(response.id.toString())
          .setData({
        'id': response.id,
        'email': response.email,
        'nickname': response.fullName,
        'vendorId': response.vendorId
      });
    } // end if we need to create a new user
  } // end function createUser

  /*
  * createUserInboxMessage - Creates an inbox chat message for a customer for storing a chat
  * fromId - Customer from ID
  * toId - Customer to ID
  * chatId - ID of the chat where messages will be stored
  * type - Type of the message (buyer or seller)
  * content - Content of the message for message preview
  * product - Product that message group is related to.
  */
  static Future createUserInboxMessage(
      int fromId,
      int toId,
      String chatId,
      String content,
      Product product,
      UserMessageType userMessageType,
      MessageType messageType) async {
    // get existing message collection
    String userMessageId;

    // setup message preview and trim if necessary
    var messagePreview = content;
    if (content.length > 50) {
      messagePreview = content.substring(0, 50);
    } // end if content length > 50

    if (userMessageType == UserMessageType.buyer) {
      userMessageId = fromId.toString() + '-' + product.id.toString();

      // custom message preview for delivery quote
      if (messageType == MessageType.deliveryQuote) {
        FirebaseDeliveryQuote deliveryQuoteMessage =
            FirebaseHelper.readDeliveryQuoteMessageContent(content);
        messagePreview = 'A delivery has been requested for ' +
            deliveryQuoteMessage.expectedDropoff;
      } // end if delivery quote message type
    } else {
      userMessageId = toId.toString() + '-' + product.id.toString();

      // custom message preview for delivery quote
      if (messageType == MessageType.deliveryQuote) {
        FirebaseDeliveryQuote deliveryQuoteMessage =
            FirebaseHelper.readDeliveryQuoteMessageContent(content);
        messagePreview = 'A delivery has been requested for ' +
            deliveryQuoteMessage.expectedPickup;
      } // end if delivery quote message type
    } // end if type is buyer

    // set up params to store for user inbox message
    var now = DateTime.now().millisecondsSinceEpoch.toString();

    // create/update a inbox message
    var data = {
      'chatId': chatId,
      'product': product.toJson(),
      'messageType': userMessageType.index,
      'messagePreview': messagePreview,
      'lastMessageTimestamp': now
    };
    if (userMessageType == UserMessageType.buyer) {
      data['toId'] = toId;
      data['fromId'] = fromId;
    } else {
      data['fromId'] = toId;
      data['toId'] = fromId;
    } // end if type is buyer

    Firestore.instance
        .collection('inbox_messages')
        .document(userMessageId)
        .setData(data);
  } // end function createUserInboxMessage

  /*
  * sendProductMessage - Send a product message from one user to another
  * chatId - Group chat ID
  * fromId - Customer from ID
  * toId - Customer to ID
  * product - Product that message group is related to.
  * content - Content of the message for message preview
  * type - Type of the message (buyer or seller)
  */
  static Future sendProductMessage(String chatId, int fromId, int toId,
      Product product, String content, MessageType messageType) async {
    await createUserInboxMessage(fromId, toId, chatId, content, product,
        UserMessageType.buyer, messageType);
    await createUserInboxMessage(fromId, toId, chatId, content, product,
        UserMessageType.seller, messageType);

    var documentReference = Firestore.instance
        .collection('messages')
        .document(chatId)
        .collection(chatId);

    if (messageType == MessageType.deliveryQuote) {
      var deliveryQuoteRef = documentReference.where('messageType',
          isEqualTo: MessageType.deliveryQuote.index);
      var deliveryQuoteDocuments = await deliveryQuoteRef.getDocuments();
      if (deliveryQuoteDocuments.documents.isNotEmpty) {
        Firestore.instance.runTransaction((transaction) async {
          await transaction
              .delete(deliveryQuoteDocuments.documents[0].reference);
        });
      }
    } // end if message type delivery quote

    Firestore.instance.runTransaction((transaction) async {
      await transaction.set(
        documentReference
            .document(DateTime.now().millisecondsSinceEpoch.toString()),
        {
          'idFrom': fromId,
          'idTo': toId,
          'messageType': messageType.index,
          'timestamp': DateTime.now().millisecondsSinceEpoch.toString(),
          'content': content
        },
      );
    });
  } // end function sendProductMessage

  /*
  * acceptDeliveryQuote - Accept a delivery quote from a group of messages
  * chatId - Group chat ID
  */
  static Future updateDeliveryQuoteStatus(
      String chatId, DeliveryQuoteStatus status) async {
    var documentReference = Firestore.instance
        .collection('messages')
        .document(chatId)
        .collection(chatId);

    var deliveryQuoteRef = documentReference.where('messageType',
        isEqualTo: MessageType.deliveryQuote.index);
    var deliveryQuoteDocuments = await deliveryQuoteRef.getDocuments();

    if (deliveryQuoteDocuments.documents.isNotEmpty) {
      var deliveryQuote = deliveryQuoteDocuments.documents[0];
      await deliveryQuote.reference
          .updateData(<String, dynamic>{'status': status.index});
    } // end if we found a delivery quote to accept
  } // end function for accepting a delivery quote

  /*
  * getUserMessagesStream - Returns a real time messages stream for the inbox
  * customerId - Customer from ID for messages
  */
  static Stream getUserMessagesStream(int customerId) {
    return Firestore.instance
        .collection('inbox_messages')
        .where(FieldPath.documentId,
            isGreaterThanOrEqualTo: customerId.toString())
        .where(FieldPath.documentId, isLessThan: (customerId + 1).toString())
        .orderBy(FieldPath.documentId)
        .limit(20)
        .snapshots();
  } // end function getUserMessagesStream

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
  } // end function getProductMessagesStream

  /*
  * deleteProductMessage - Delete a product message
  * chatId - Group chat ID
  * messageId - Message ID
  */
  static Future deleteProductMessage(String chatId, String messageId) async {
    await Firestore.instance
        .collection('messages')
        .document(chatId)
        .collection(chatId)
        .document(messageId)
        .delete();
  } // end function sendProductMessage

  /*
  * configure - Configure Firebase settings
  */
  static Future configure() async {
    await Firestore.instance.settings(persistenceEnabled: false);
  } // end function configure

} // end class Firebase
