import 'package:resold/models/order.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';

class OrdersState {
  List<Order> purchasedOrders;
  List<Order> soldOrders;
  List<FirebaseDeliveryQuote> requestedDeliveries;

  OrdersState({this.purchasedOrders, this.soldOrders, this.requestedDeliveries});

  factory OrdersState.initialState() {
    return OrdersState();
  } // end function initialState
}
