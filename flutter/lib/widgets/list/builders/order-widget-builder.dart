import 'package:flutter/widgets.dart';
import 'package:resold/helpers/filters/orders-filter.dart';
import 'package:resold/models/order.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/list/order-list.dart';

class OrderWidgetBuilder {
  static Widget buildOrderWidget(CustomerResponse customer, List<Order> orders, {String error}) {
    List<Order> inProgressOrders = OrdersFilter.filterInProgress(orders);
    List<Order> completedOrders = OrdersFilter.filterComplete(orders);
    if (inProgressOrders.length == 0 && completedOrders.length == 0) {
      return Center(child: Text(error));
    } // end if no deliveries

    return SingleChildScrollView(
        child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Container(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          if (inProgressOrders.length > 0)
            OrderList(
                customer: customer,
                orders: inProgressOrders,
                header: Padding(
                  padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                  child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                )),
          if (completedOrders.length > 0)
            OrderList(
                customer: customer,
                orders: completedOrders,
                header: Padding(
                  padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                  child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                )),
        ]))
      ],
    ));
  } // end function buildOrdersWidget
}
