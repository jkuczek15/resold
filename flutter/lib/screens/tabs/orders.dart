import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/helpers/filters/orders-filter.dart';
import 'package:resold/models/order.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/list/order-list.dart';

class OrdersPage extends StatelessWidget {
  final List<Order> purchasedOrders;
  final List<Order> soldOrders;
  final CustomerResponse customer;

  OrdersPage({this.customer, this.purchasedOrders, this.soldOrders});

  @override
  Widget build(BuildContext context) {
    List<Order> inProgressPurchasedOrders = OrdersFilter.filterInProgress(purchasedOrders);
    List<Order> completedPurchasedOrders = OrdersFilter.filterComplete(purchasedOrders);
    List<Order> inProgressSoldOrders = OrdersFilter.filterInProgress(soldOrders);
    List<Order> completedSoldOrders = OrdersFilter.filterComplete(soldOrders);
    return LiquidPullToRefresh(
        height: 80,
        springAnimationDurationInMilliseconds: 500,
        onRefresh: () async {
          // setState(() {
          //   futurePurchasedOrders = Magento.getPurchasedOrders(customer.id);
          //   futureSoldOrders = ResoldRest.getVendorOrders(customer.token);
          // });
        },
        showChildOpacityTransition: false,
        color: ResoldBlue,
        animSpeedFactor: 5.0,
        child: DefaultTabController(
          length: 2,
          child: Scaffold(
              appBar: AppBar(
                  backgroundColor: Colors.white,
                  toolbarHeight: 74,
                  bottom: TabBar(
                    indicatorColor: ResoldBlue,
                    tabs: [
                      Tab(icon: Icon(MdiIcons.cart, semanticLabel: 'Purchased'), text: 'Purchased'),
                      Tab(icon: Icon(MdiIcons.clipboardText, semanticLabel: 'Sold'), text: 'Sold')
                    ],
                  )),
              body: TabBarView(children: [
                (() {
                  if (purchasedOrders.length == 0) {
                    return Center(child: Text('You haven\'t purchased any items.'));
                  }
                  if (inProgressPurchasedOrders.length > 0 && completedPurchasedOrders.length > 0) {
                    return SingleChildScrollView(
                        child: Expanded(
                            child: Column(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                          child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                        ),
                        OrderList(customer: customer, orders: inProgressPurchasedOrders),
                        SizedBox(height: 10),
                        Padding(
                          padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                          child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                        ),
                        OrderList(customer: customer, orders: completedPurchasedOrders),
                      ],
                    )));
                  } else if (inProgressPurchasedOrders.length > 0) {
                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                          child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                        ),
                        OrderList(customer: customer, orders: inProgressPurchasedOrders)
                      ],
                    );
                  } else if (completedPurchasedOrders.length > 0) {
                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                          child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                        ),
                        Expanded(child: OrderList(customer: customer, orders: completedPurchasedOrders))
                      ],
                    );
                  } // end if completed purchased orders
                }()),
                (() {
                  if (soldOrders.length == 0) {
                    return Center(child: Text('You haven\'t sold any items.'));
                  }
                  if (inProgressSoldOrders.length > 0 && completedSoldOrders.length > 0) {
                    return SingleChildScrollView(
                        child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                          child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                        ),
                        OrderList(customer: customer, orders: inProgressSoldOrders),
                        SizedBox(height: 10),
                        Padding(
                          padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                          child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                        ),
                        OrderList(customer: customer, orders: completedSoldOrders)
                      ],
                    ));
                  } else if (inProgressSoldOrders.length > 0) {
                    return SingleChildScrollView(
                        child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                          child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                        ),
                        OrderList(customer: customer, orders: inProgressSoldOrders)
                      ],
                    ));
                  } else if (completedSoldOrders.length > 0) {
                    return SingleChildScrollView(
                        child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                          child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                        ),
                        OrderList(customer: customer, orders: completedSoldOrders)
                      ],
                    ));
                  } // end if completed sold orders
                }())
              ])),
        ));
  } // end function build
}
