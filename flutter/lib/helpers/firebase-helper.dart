import 'package:intl/intl.dart';
import 'package:money2/money2.dart';
import 'package:resold/models/product.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/firebase/firebase-offer.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class FirebaseHelper {
  /*
  * buildDeliveryQuote - Build delivery quote from a document
  * document - Firebase message content
  */
  static FirebaseDeliveryQuote buildDeliveryQuote(String content,
      {String chatId, CustomerResponse fromCustomer, CustomerResponse toCustomer, Product product}) {
    List<String> contentParts = content.split('|');

    return FirebaseDeliveryQuote(
        quoteId: contentParts[0],
        chatId: chatId,
        fromCustomer: fromCustomer,
        toCustomer: toCustomer,
        product: product,
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
  static FirebaseOffer buildOffer(String content) {
    List<String> contentParts = content.split('|');
    return FirebaseOffer(fromId: int.tryParse(contentParts[0]), price: int.tryParse(contentParts[1]));
  } // end function readOfferMessageContent
}
