import 'package:resold/models/order.dart';

class OrdersState {
  List<Order> purchasedOrders;
  List<Order> soldOrders;

  OrdersState({this.purchasedOrders, this.soldOrders});
}
