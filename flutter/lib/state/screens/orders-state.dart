import 'package:resold/models/order.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class OrdersState {
  List<Order> purchasedOrders;
  List<Order> soldOrders;
  List<FirebaseDeliveryQuote> requestedDeliveries;

  OrdersState({this.purchasedOrders, this.soldOrders, this.requestedDeliveries});

  static Future<OrdersState> initialState(CustomerResponse customer) async {
    OrdersState ordersState = OrdersState();
    if (customer.isLoggedIn()) {
      await Future.wait([
        Magento.getPurchasedOrders(customer.id),
        ResoldRest.getVendorOrders(customer.token),
        ResoldFirebase.getRequestedDeliveryQuotes(customer)
      ]).then((data) {
        ordersState.purchasedOrders = data[0];
        ordersState.soldOrders = data[1];
        ordersState.requestedDeliveries = data[2];
      });
    } // end if customer is logged in
    return ordersState;
  } // end function initialState
}
