import 'package:money2/money2.dart';
import 'package:resold/enums/delivery-quote-status.dart';

class FirebaseDeliveryQuote {
  int idFrom;
  int idTo;
  String chatId;
  String quoteId;
  int productId;
  Money fee;
  DeliveryQuoteStatus status;
  String expectedPickup;
  String expectedDropoff;

  FirebaseDeliveryQuote({
    this.idFrom,
    this.idTo,
    this.chatId,
    this.quoteId,
    this.productId,
    this.fee,
    this.status,
    this.expectedDropoff,
    this.expectedPickup,
  });
}
