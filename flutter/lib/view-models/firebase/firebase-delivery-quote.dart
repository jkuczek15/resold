import 'package:money2/money2.dart';
import 'package:resold/services/firebase.dart';

class FirebaseDeliveryQuote {
  String quoteId;
  Money fee;
  String expectedPickup;
  String expectedDropoff;

  FirebaseDeliveryQuote(
      {this.quoteId, this.fee, this.expectedDropoff, this.expectedPickup});
}
