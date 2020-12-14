import 'package:flutter/widgets.dart';
import 'package:resold/helpers/filters/orders-filter.dart';
import 'package:resold/models/order.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/list/order-list.dart';

class OrderWidgetBuilder {
  static Widget buildOrderWidget(CustomerResponse customer, List<Order> orders) {
    Widget ordersWidget;
    List<Order> inProgressOrders = OrdersFilter.filterInProgress(orders);
    List<Order> completedOrders = OrdersFilter.filterComplete(orders);
    if (inProgressOrders.length > 0 && completedOrders.length > 0) {
      ordersWidget = Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Padding(
              padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
              child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
            ),
            OrderList(customer: customer, orders: inProgressOrders),
            SizedBox(height: 10),
            Padding(
              padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
              child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
            ),
            Expanded(child: OrderList(customer: customer, orders: completedOrders)),
          ]))
        ],
      );
    } else if (inProgressOrders.length > 0) {
      ordersWidget = Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
            child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
          ),
          Expanded(child: OrderList(customer: customer, orders: inProgressOrders))
        ],
      );
    } else if (completedOrders.length > 0) {
      ordersWidget = Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
            child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
          ),
          Expanded(child: OrderList(customer: customer, orders: completedOrders))
        ],
      );
    } // end if completed purchased orders
    return ordersWidget;
  }
}
