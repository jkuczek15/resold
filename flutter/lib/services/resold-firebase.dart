import 'dart:async';
import 'package:resold/enums/delivery-quote-status.dart';
import 'package:resold/enums/message-type.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/firebase/firebase-offer.dart';
import 'package:resold/view-models/firebase/inbox-message.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:resold/enums/user-message-type.dart';
import 'package:resold/helpers/firebase-helper.dart';
import 'package:rxdart/rxdart.dart';

/*
* Resold Firebase API service - Firebase specific API client
* This service is used to make real-time requests
*/
class ResoldFirebase {
  static FirebaseFirestore firestore = FirebaseFirestore.instance;

  /*
  * createOrUpdateUser - Create a Firebase user given customer data
  * customer - Customer response data
  */
  static Future createOrUpdateUser(CustomerResponse customer) async {
    // check if we have a firebase user
    await firestore.collection('users').doc(customer.id.toString()).set({
      'id': customer.id,
      'email': customer.email,
      'nickname': customer.fullName,
      'vendorId': customer.vendorId,
      'deviceToken': customer.deviceToken
    });
  } // end function createUser

  /*
  * getUserInboxMessage - Retreive a user inbox message
  * customer - Customer response data
  */
  static Future<InboxMessage> getUserInboxMessage(String chatId) async {
    // check if we have a firebase user
    DocumentSnapshot document = await firestore.collection('inbox_messages').doc(chatId).get();
    return InboxMessage(
        chatId: document['chatId'],
        fromId: document['fromId'],
        toId: document['toId'],
        lastMessageTimestamp: document['lastMessageTimestamp'],
        messagePreview: document['messagePreview'],
        messageType: UserMessageType.values[document['messageType']],
        product: Product.fromJson(document['product'], parseId: false),
        unread: document['unread']);
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
  static Future createUserInboxMessage(int fromId, int toId, String chatId, String content, Product product,
      UserMessageType userMessageType, MessageType messageType, bool isSeller) async {
    // get existing message collection
    String userMessageId =
        (userMessageType == UserMessageType.sender ? fromId.toString() : toId.toString()) + '-' + product.id.toString();

    // setup message preview and trim if necessary
    var messagePreview = content;
    if (content.length > 50) {
      messagePreview = content.substring(0, 50);
    } // end if content length > 50

    if (messageType == MessageType.offer) {
      FirebaseOffer offerMessage = FirebaseHelper.buildOffer(content);

      if (userMessageType == UserMessageType.sender) {
        messagePreview = 'You have sent an offer for \$${offerMessage.price}.';
      } else {
        messagePreview = 'You have received an offer for \$${offerMessage.price}.';
      } // end if user message type is seller
    } else if (messageType == MessageType.deliveryQuote) {
      FirebaseDeliveryQuote deliveryQuoteMessage =
          FirebaseHelper.buildDeliveryQuote(content, chatId: chatId, idFrom: fromId, idTo: toId);
      if (isSeller) {
        messagePreview = 'Delivery has been requested for ' + deliveryQuoteMessage.expectedPickup;
      } else {
        messagePreview = 'Delivery has been requested for ' + deliveryQuoteMessage.expectedDropoff;
      } // end if user is seller
    } // end if message type is offer

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
    if (userMessageType == UserMessageType.sender) {
      data['toId'] = toId;
      data['fromId'] = fromId;
    } else {
      data['fromId'] = toId;
      data['toId'] = fromId;
    } // end if type is buyer

    // set message to unread by default
    data['unread'] = userMessageType == UserMessageType.receiver;
    await firestore.collection('inbox_messages').doc(userMessageId).set(data);
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
  static Future sendProductMessage(
      String chatId, int fromId, int toId, Product product, String content, MessageType messageType, bool isSeller,
      {bool firstMessage = false}) async {
    // mark buyer's inbox message as unread if sender is the seller and not the first message
    await createUserInboxMessage(fromId, toId, chatId, content, product, UserMessageType.sender, messageType, isSeller);

    // mark seller's inbox message as unread if sender is the buyer
    await createUserInboxMessage(
        fromId, toId, chatId, content, product, UserMessageType.receiver, messageType, isSeller);

    // get the message bucket
    CollectionReference collectionReference = firestore.collection('messages').doc(chatId).collection(chatId);

    // message data to be stored
    Map<String, dynamic> data = {
      'idFrom': fromId,
      'idTo': toId,
      'messageType': messageType.index,
      'timestamp': DateTime.now().millisecondsSinceEpoch.toString(),
      'content': content
    };

    // check if we are storing a delivery quote
    if (messageType == MessageType.deliveryQuote) {
      Query deliveryQuoteRef = collectionReference.where('messageType', isEqualTo: MessageType.deliveryQuote.index);
      QuerySnapshot deliveryQuoteDocuments = await deliveryQuoteRef.get();
      if (deliveryQuoteDocuments.docs.isNotEmpty) {
        await firestore.runTransaction((transaction) async {
          transaction.delete(deliveryQuoteDocuments.docs[0].reference);
        });
      } // end if we have an existing delivery quote

      // initial delivery quote status
      data['status'] = DeliveryQuoteStatus.none.index;
    } // end if message type delivery quote

    await firestore.runTransaction((transaction) async {
      transaction.set(collectionReference.doc(DateTime.now().millisecondsSinceEpoch.toString()), data);
    });
  } // end function sendProductMessage

  /*
  * updateDeliveryQuoteStatus - Change the status of a delivery quote
  * chatId - Group chat ID
  * status - Delivery quote status
  */
  static Future updateDeliveryQuoteStatus(String chatId, DeliveryQuoteStatus status) async {
    CollectionReference documentReference = firestore.collection('messages').doc(chatId).collection(chatId);

    Query deliveryQuoteRef = documentReference.where('messageType', isEqualTo: MessageType.deliveryQuote.index);
    QuerySnapshot deliveryQuoteDocuments = await deliveryQuoteRef.get();

    if (deliveryQuoteDocuments.docs.isNotEmpty) {
      DocumentSnapshot deliveryQuote = deliveryQuoteDocuments.docs[0];
      await deliveryQuote.reference.update(<String, dynamic>{'status': status.index});
      await markInboxMessageUnread(chatId);
    } // end if we found a delivery quote to accept
  } // end function for accepting a delivery quote

  /*
  * getRequestedDeliveryQuotes - Return requested delivery quotes for a customer
  * customerId - Customer ID
  */
  static Future<List<FirebaseDeliveryQuote>> getRequestedDeliveryQuotes(int customerId) async {
    QuerySnapshot chatDocuments =
        await firestore.collection('inbox_messages').where('fromId', isEqualTo: customerId).get();

    List<FirebaseDeliveryQuote> requestedDeliveries = new List<FirebaseDeliveryQuote>();
    if (chatDocuments.docs.isNotEmpty) {
      for (int i = 0; i < chatDocuments.size; i++) {
        String chatId = chatDocuments.docs[i]['chatId'];
        QuerySnapshot deliveryQuoteDocuments = await firestore
            .collection('messages')
            .doc(chatId)
            .collection(chatId)
            .where('idFrom', isEqualTo: customerId)
            .where('messageType', isEqualTo: MessageType.deliveryQuote.index)
            .get();
        if (deliveryQuoteDocuments.docs.isNotEmpty) {
          for (int j = 0; j < deliveryQuoteDocuments.size; j++) {
            DocumentSnapshot deliveryQuote = deliveryQuoteDocuments.docs[0];
            requestedDeliveries.add(FirebaseHelper.buildDeliveryQuote(deliveryQuote['content'],
                chatId: chatId, idFrom: deliveryQuote['idFrom'], idTo: deliveryQuote['idTo']));
          } // end foreach loop over delivery quote documents
        } // end if we found a delivery quote to accept
      } // end foreach loop over delivery quote documents
    } // end if chat documents is not empty

    return requestedDeliveries;
  } // end function for accepting a delivery quote

  /*
  * setMessageProduct - Set the product for an inbox message
  * chatId - Group chat ID
  * product - Product to be set
  */
  static Future setMessageProduct(String chatId, Product product) async {
    DocumentReference documentReference = firestore.collection('inbox_messages').doc(chatId);
    if (documentReference != null) {
      await documentReference.update(<String, dynamic>{'product': product.toJson()});
    } // end if we found a delivery quote to accept
  } // end function for accepting a delivery quote

  /*
  * getUserMessagesStream - Returns a real time messages stream for the inbox
  * customerId - Customer from ID for messages
  */
  static Stream<List<QuerySnapshot>> getUserMessagesStream(int customerId) {
    // get the to stream messages
    Stream toStream = firestore
        .collection('inbox_messages')
        .where(FieldPath.documentId, isGreaterThanOrEqualTo: customerId.toString())
        .where(FieldPath.documentId, isLessThan: (customerId + 1).toString())
        .where('toId', isEqualTo: customerId)
        .orderBy(FieldPath.documentId)
        .limit(20)
        .snapshots();

    // get the from stream messages
    Stream fromStream = firestore
        .collection('inbox_messages')
        .where(FieldPath.documentId, isGreaterThanOrEqualTo: customerId.toString())
        .where(FieldPath.documentId, isLessThan: (customerId + 1).toString())
        .where('fromId', isEqualTo: customerId)
        .orderBy(FieldPath.documentId)
        .limit(20)
        .snapshots();

    return CombineLatestStream.list([toStream, fromStream]);
  } // end function getUserMessagesStream

  /*
  * getProductMessagesStream - Returns a real time messages stream for a certain chat group
  * chatId - Chat ID for product messages
  */
  static Stream getProductMessagesStream(String chatId) {
    return firestore
        .collection('messages')
        .doc(chatId)
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
    await firestore.collection('messages').doc(chatId).collection(chatId).doc(messageId).delete();
  } // end function sendProductMessage

  /*
  * getUnreadMessageCount - Gets the number of unread inbox messages
  * customerId - Customer ID
  */
  static Stream getUnreadMessageCount(int customerId) {
    return FirebaseFirestore.instance
        .collection('inbox_messages')
        .where('fromId', isEqualTo: customerId)
        .where('unread', isEqualTo: true)
        .snapshots();
  } // end function getUnreadMessageCount

  /*
  * markInboxMessageRead - Marks the inbox message as read
  * chatId - Inbox message chat id
  */
  static Future markInboxMessageRead(String documentId) async {
    DocumentReference documentReference = firestore.collection('inbox_messages').doc(documentId);
    await documentReference.update(<String, dynamic>{'unread': false});
  } // end function markInboxMessageRead

  /*
  * markInboxMessageUnread - Marks the inbox message as unread
  * chatId - Inbox message chat id
  */
  static Future markInboxMessageUnread(String chatId) async {
    QuerySnapshot inboxMessages = await firestore
        .collection('inbox_messages')
        .where('messageType', isEqualTo: UserMessageType.sender.index)
        .where('chatId', isEqualTo: chatId)
        .get();

    if (inboxMessages.docs.isNotEmpty) {
      DocumentSnapshot inboxMessage = inboxMessages.docs[0];
      await inboxMessage.reference.update(<String, dynamic>{'unread': true});
    } // end if we found a delivery quote to accept
  } // end function markInboxMessageUnread

  /*
  * getDeviceToken - Get the device token for a specific user
  * customerId - Customer ID
  */
  static Future<String> getDeviceToken(int customerId) async {
    DocumentSnapshot document = await firestore.collection('users').doc(customerId.toString()).get();
    return document.exists ? document['deviceToken'] : null;
  } // end function markInboxMessageRead

  /*
  * configure - Configure Firebase settings
  */
  static Future configure() async {
    firestore.settings = Settings(persistenceEnabled: false);
  } // end function configure

} // end class Firebase
