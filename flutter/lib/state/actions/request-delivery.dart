import 'package:rebloc/rebloc.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';

class RequestDeliveryAction extends Action {
  final FirebaseDeliveryQuote quote;

  const RequestDeliveryAction({this.quote});
}
