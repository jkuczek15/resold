import 'package:resold/models/order.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';

class OrdersState {
  List<Order> purchasedOrders;
  List<Order> soldOrders;
  List<FirebaseDeliveryQuote> requestedPurchaseDeliveries;
  List<FirebaseDeliveryQuote> requestedSoldDeliveries;

  OrdersState({this.purchasedOrders, this.soldOrders, this.requestedPurchaseDeliveries, this.requestedSoldDeliveries});

  factory OrdersState.initialState() {
    return OrdersState();
  } // end function initialState
}
