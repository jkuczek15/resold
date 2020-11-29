import 'dart:async';
import 'dart:convert';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:http/http.dart';
import 'package:intl/intl.dart';
import 'package:money2/money2.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/firebase/firebase-offer.dart';

class FirebaseHelper {
  static final String serverToken = '<Server-Token>';
  static final FirebaseMessaging firebaseMessaging = FirebaseMessaging();
  static Client client = Client();

  /*
  * backgroundMessageHandler - Handle background notification messages from Firebase
  * message - Firebase message
  */
  static Future<dynamic> backgroundMessageHandler(Map<String, dynamic> message) async {
    if (message.containsKey('data')) {
      // Handle data message
      final dynamic data = message['data'];
    }

    if (message.containsKey('notification')) {
      // Handle notification message
      final dynamic notification = message['notification'];
    }
    // Or do other work.
  } // end function backgroundMessageHandler

  /*
  * sendNotificationMessage - Send a Firebase notification message
  * message - Firebase message
  */
  static Future<Map<String, dynamic>> sendNotificationMessage() async {
    await firebaseMessaging.requestNotificationPermissions(
      const IosNotificationSettings(sound: true, badge: true, alert: true, provisional: false),
    );

    await client.post(
      'https://fcm.googleapis.com/fcm/send',
      headers: <String, String>{
        'Content-Type': 'application/json',
        'Authorization': 'key=$serverToken',
      },
      body: jsonEncode(
        <String, dynamic>{
          'notification': <String, dynamic>{'body': 'this is a body', 'title': 'this is a title'},
          'priority': 'high',
          'data': <String, dynamic>{'click_action': 'FLUTTER_NOTIFICATION_CLICK', 'id': '1', 'status': 'done'},
          'to': await firebaseMessaging.getToken(),
        },
      ),
    );

    final Completer<Map<String, dynamic>> completer = Completer<Map<String, dynamic>>();

    firebaseMessaging.configure(
      onMessage: (Map<String, dynamic> message) async {
        completer.complete(message);
      },
    );

    return completer.future;
  } // end function sendNotificationMessage

  /*
  * readDeliveryQuoteMessageContent - Parse delivery quote message content
  * content - Firebase message content
  */
  static FirebaseDeliveryQuote readDeliveryQuoteMessageContent(String content) {
    var contentParts = content.split('|');

    return FirebaseDeliveryQuote(
        quoteId: contentParts[0],
        fee: Money.fromInt(int.tryParse(contentParts[1]), Currency.create('USD', 2)),
        expectedPickup: DateFormat('h:mm a on MM/dd/yyyy.')
            .format(DateTime.tryParse(DateTime.now().add(Duration(minutes: int.tryParse(contentParts[2]))).toString()))
            .replaceAll(new RegExp(r'on ' + DateFormat('MM/dd/yyyy').format(DateTime.now()) + '.'), ''),
        expectedDropoff: DateFormat('h:mm a on MM/dd/yyyy.')
            .format(DateTime.tryParse(DateTime.now().add(Duration(minutes: int.tryParse(contentParts[3]))).toString()))
            .replaceAll(new RegExp(r'on ' + DateFormat('MM/dd/yyyy').format(DateTime.now()) + '.'), ''));
  } // end function readDeliveryQuoteMessageContent

  /*
  * readOfferMessageContent - Parse offer message content
  * content - Firebase message content
  */
  static FirebaseOffer readOfferMessageContent(String content) {
    var contentParts = content.split('|');
    return FirebaseOffer(fromId: int.tryParse(contentParts[0]), price: int.tryParse(contentParts[1]));
  } // end function readOfferMessageContent
}
