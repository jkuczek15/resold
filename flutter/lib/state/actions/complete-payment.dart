import 'package:rebloc/rebloc.dart';

class CompletePaymentAction extends Action {
  final String chatId;

  const CompletePaymentAction(this.chatId);
}
