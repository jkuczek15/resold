import 'package:money2/money2.dart';
import 'package:resold/enums/delivery-quote-status.dart';
import 'package:resold/models/product.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class FirebaseDeliveryQuote {
  CustomerResponse fromCustomer;
  CustomerResponse toCustomer;
  int sellerCustomerId;
  Product product;
  String chatId;
  String quoteId;
  Money fee;
  DeliveryQuoteStatus status;
  String expectedPickup;
  String expectedDropoff;

  FirebaseDeliveryQuote({
    this.fromCustomer,
    this.toCustomer,
    this.sellerCustomerId,
    this.product,
    this.chatId,
    this.quoteId,
    this.fee,
    this.status,
    this.expectedDropoff,
    this.expectedPickup,
  });
}
