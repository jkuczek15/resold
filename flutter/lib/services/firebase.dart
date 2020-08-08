import 'package:http/http.dart' show Client;
import 'dart:async';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:cloud_firestore/cloud_firestore.dart';

class Firebase {

  static Config config = Config();
  static Client client = Client();

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
  }

  static Future sendProductMessage(int fromId, int toId, String content, int type) async {

    var chatId = fromId.toString() + '-' + toId.toString();

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
  }

  static Stream getUserMessagesStream() {
    return Firestore.instance.collection('users').snapshots();
  }

  static Stream getProductMessagesStream(String chatId) {
    return Firestore.instance
        .collection('messages')
        .document(chatId)
        .collection(chatId)
        .orderBy('timestamp', descending: true)
        .limit(20)
        .snapshots();
  }
}

class Config {
  String baseUrl;
  String accessToken;
  Map<String, String> adminHeaders = Map<String, String>();
  Map<String, String> customerHeaders = Map<String, String>();
  Future initialized;

  Config() {
    initialized = init();
  }

  init() async {
    final config = {
      'base_url': 'https://resold.us/rest/V1',
      'access_token': 'frlf1x1o9edlk8q77reqmfdlbk54fycl'
    };

    baseUrl = config['base_url'];
    accessToken = config['access_token'];
    adminHeaders['Authorization'] = 'Bearer ${this.accessToken}';
    adminHeaders['User-Agent'] = customerHeaders['User-Agent'] = 'Resold - Mobile Application';
    adminHeaders['Content-Type'] = customerHeaders['User-Agent'] = 'application/json';
  }
}
