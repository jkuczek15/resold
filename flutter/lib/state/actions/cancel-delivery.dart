import 'package:rebloc/rebloc.dart';

class CancelDeliveryAction extends Action {
  final String chatId;

  const CancelDeliveryAction(this.chatId);
}
