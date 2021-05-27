import 'package:geolocator/geolocator.dart';
import 'package:resold/models/product.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class MessagePageArguments {
  final CustomerResponse fromCustomer;
  final CustomerResponse toCustomer;
  final Position currentLocation;
  final Product product;
  final String chatId;
  final Function dispatcher;

  MessagePageArguments(
      {this.fromCustomer, this.toCustomer, this.currentLocation, this.product, this.chatId, this.dispatcher});
}
