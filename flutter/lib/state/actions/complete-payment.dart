import 'package:rebloc/rebloc.dart';
import 'package:resold/models/order.dart';

class CompletePaymentAction extends Action {
  final Order order;
  final String chatId;

  const CompletePaymentAction(this.order, this.chatId);
}
