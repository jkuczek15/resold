import 'package:intl/intl.dart';
import 'package:money2/money2.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/firebase/firebase-offer.dart';

class FirebaseHelper {
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

  static FirebaseOffer readOfferMessageContent(String content) {
    var contentParts = content.split('|');
    return FirebaseOffer(fromId: int.tryParse(contentParts[0]), price: int.tryParse(contentParts[1]));
  } // end function readOfferMessageContent
}
