import 'package:resold/enums/user-message-type.dart';
import 'package:resold/models/product.dart';

class InboxMessage {
  String chatId;
  int fromId;
  int toId;
  String lastMessageTimestamp;
  String messagePreview;
  UserMessageType messageType;
  Product product;
  bool unread;

  InboxMessage(
      {this.chatId,
      this.fromId,
      this.toId,
      this.lastMessageTimestamp,
      this.messagePreview,
      this.messageType,
      this.product,
      this.unread});
}
