import 'package:resold/models/order.dart';

class OrdersFilter {
  static List<Order> filterInProgress(List<Order> orders) {
    return orders
        .where((order) =>
            order.status == 'pending' ||
            order.status == 'processing' ||
            order.status == 'pickup' ||
            order.status == 'delivery_in_progress')
        .toList();
  } // end function filterInProgress

  static List<Order> filterComplete(List<Order> orders) {
    return orders.where((order) => order.status == 'complete').toList();
  } // end function filterInProgress
}
