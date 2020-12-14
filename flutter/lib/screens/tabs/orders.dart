import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/models/order.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/state/actions/set-orders-state.dart';
import 'package:resold/state/screens/orders-state.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/list/builders/order-widget-builder.dart';

class OrdersPage extends StatelessWidget {
  final CustomerResponse customer;
  final List<Order> purchasedOrders;
  final List<Order> soldOrders;
  final List<FirebaseDeliveryQuote> requestedPurchaseDeliveries;
  final List<FirebaseDeliveryQuote> requestedSoldDeliveries;
  final Function dispatcher;

  OrdersPage(
      {this.customer,
      this.purchasedOrders,
      this.soldOrders,
      this.requestedPurchaseDeliveries,
      this.requestedSoldDeliveries,
      this.dispatcher});

  @override
  Widget build(BuildContext context) {
    return LiquidPullToRefresh(
        height: 80,
        springAnimationDurationInMilliseconds: 500,
        onRefresh: () async {
          dispatcher(SetOrdersStateAction(OrdersState(
              purchasedOrders: await Magento.getPurchasedOrders(customer.id),
              soldOrders: await ResoldRest.getVendorOrders(customer.token),
              requestedPurchaseDeliveries: await ResoldFirebase.getRequestedDeliveryQuotes(customer.id),
              requestedSoldDeliveries: await ResoldFirebase.getRequestedDeliveryQuotes(customer.id))));
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
                  return OrderWidgetBuilder.buildOrderWidget(customer, purchasedOrders, requestedPurchaseDeliveries,
                      error: 'You haven\'t purchased any items.');
                }()),
                (() {
                  return OrderWidgetBuilder.buildOrderWidget(customer, soldOrders, requestedSoldDeliveries,
                      error: 'You haven\'t sold any items.');
                }())
              ])),
        ));
  } // end function build
}
