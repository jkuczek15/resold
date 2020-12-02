import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/models/order.dart';
import 'package:resold/models/product.dart';
import 'package:resold/screens/order/details.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';
import 'package:intl/intl.dart';

class OrdersPage extends StatefulWidget {
  final CustomerResponse customer;

  OrdersPage({CustomerResponse customer, Key key})
      : customer = customer,
        super(key: key);

  @override
  OrdersPageState createState() => OrdersPageState(this.customer);
}

class OrdersPageState extends State<OrdersPage> {
  Future<List<Order>> futurePurchasedOrders;
  Future<List<Order>> futureSoldOrders;
  final CustomerResponse customer;

  OrdersPageState(this.customer);

  @override
  void initState() {
    super.initState();
    futurePurchasedOrders = Magento.getPurchasedOrders(customer.id);
    futureSoldOrders = ResoldRest.getVendorOrders(customer.token);
  } // end function initState

  @override
  Widget build(BuildContext context) {
    NumberFormat formatter = new NumberFormat("\$###,###", "en_US");
    return LiquidPullToRefresh(
        height: 80,
        springAnimationDurationInMilliseconds: 500,
        onRefresh: () async {
          setState(() {
            futurePurchasedOrders = Magento.getPurchasedOrders(customer.id);
            futureSoldOrders = ResoldRest.getVendorOrders(customer.token);
          });
        },
        showChildOpacityTransition: false,
        color: ResoldBlue,
        animSpeedFactor: 5.0,
        child: FutureBuilder<List<List<Order>>>(
            future: Future.wait([futurePurchasedOrders, futureSoldOrders]),
            builder: (context, snapshot) {
              if (!snapshot.hasData) {
                return Center(child: Loading());
              } // end if loading
              List<Order> purchasedOrders = snapshot.data[0];
              List<Order> soldOrders = snapshot.data[1];

              // fetch in progress purchase orders
              List<Order> inProgressPurchasedOrders = purchasedOrders
                  .where((order) =>
                      order.status == 'processing' ||
                      order.status == 'pickup' ||
                      order.status == 'delivery_in_progress')
                  .toList();

              // fetch purchased completed orders
              List<Order> completedPurchasedOrders =
                  purchasedOrders.where((order) => order.status == 'complete').toList();

              // fetch in progress soldorders
              List<Order> inProgressSoldOrders = soldOrders
                  .where((order) =>
                      order.status == 'processing' ||
                      order.status == 'pickup' ||
                      order.status == 'delivery_in_progress')
                  .toList();

              // fetch completed sold orders
              List<Order> completedSoldOrders = soldOrders.where((order) => order.status == 'complete').toList();

              return DefaultTabController(
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
                              child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Padding(
                                padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                                child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                              ),
                              ListView(
                                  padding: const EdgeInsets.all(8),
                                  shrinkWrap: true,
                                  children: List.generate(
                                    inProgressPurchasedOrders.length,
                                    (index) {
                                      Order order = inProgressPurchasedOrders[index];
                                      OrderLine line = order.items[0];
                                      Duration difference;
                                      if (order.status == 'pickup' || order.status == 'delivery_in_progress') {
                                        difference = order.dropoffEta.difference(DateTime.now());
                                      } // end if pickup or delivery in progress
                                      return InkWell(
                                          onTap: () async {
                                            // show a loading indicator
                                            showDialog(
                                                context: context,
                                                builder: (BuildContext context) {
                                                  return Center(child: Loading());
                                                });

                                            // fetch the product
                                            Product product =
                                                await ResoldRest.getProduct(customer.token, line.productId);

                                            Navigator.of(context, rootNavigator: true).pop('dialog');

                                            // navigate to order details page
                                            Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        OrderDetails(order: order, product: product, isSeller: false)));
                                          },
                                          child: Card(
                                              child: ListTile(
                                                  trailing: Text(formatter.format(line.price.round())),
                                                  title: Container(
                                                    child: Column(
                                                      crossAxisAlignment: CrossAxisAlignment.start,
                                                      children: [
                                                        Text(line.name),
                                                        order.status == 'pickup' ||
                                                                order.status == 'delivery_in_progress'
                                                            ? Text('Arriving in ${difference.inMinutes} minutes',
                                                                style: TextStyle(color: Colors.grey, fontSize: 12))
                                                            : SizedBox(),
                                                      ],
                                                    ),
                                                  ))));
                                    },
                                  )),
                              SizedBox(height: 10),
                              Padding(
                                padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                                child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                              ),
                              ListView(
                                  padding: const EdgeInsets.all(8),
                                  shrinkWrap: true,
                                  children: List.generate(
                                    completedPurchasedOrders.length,
                                    (index) {
                                      Order order = completedPurchasedOrders[index];
                                      OrderLine line = order.items[0];
                                      return InkWell(
                                          onTap: () async {
                                            // show a loading indicator
                                            showDialog(
                                                context: context,
                                                builder: (BuildContext context) {
                                                  return Center(child: Loading());
                                                });

                                            // fetch the product
                                            Product product =
                                                await ResoldRest.getProduct(customer.token, line.productId);

                                            Navigator.of(context, rootNavigator: true).pop('dialog');

                                            // navigate to order details page
                                            Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        OrderDetails(order: order, product: product, isSeller: false)));
                                          },
                                          child: Card(
                                              child: ListTile(
                                                  trailing: Text(formatter.format(line.price.round())),
                                                  title: Container(
                                                    height: 50,
                                                    child: Row(
                                                      children: [Text(line.name)],
                                                    ),
                                                  ))));
                                    },
                                  ))
                            ],
                          ));
                        } else if (inProgressPurchasedOrders.length > 0) {
                          return Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Padding(
                                padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                                child: Text('In Progress', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                              ),
                              ListView(
                                  padding: const EdgeInsets.all(8),
                                  shrinkWrap: true,
                                  children: List.generate(
                                    inProgressPurchasedOrders.length,
                                    (index) {
                                      Order order = inProgressPurchasedOrders[index];
                                      OrderLine line = order.items[0];
                                      return InkWell(
                                          onTap: () async {
                                            // show a loading indicator
                                            showDialog(
                                                context: context,
                                                builder: (BuildContext context) {
                                                  return Center(child: Loading());
                                                });

                                            // fetch the product
                                            Product product =
                                                await ResoldRest.getProduct(customer.token, line.productId);

                                            Navigator.of(context, rootNavigator: true).pop('dialog');

                                            // navigate to order details page
                                            Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        OrderDetails(order: order, product: product, isSeller: false)));
                                          },
                                          child: Card(
                                              child: ListTile(
                                                  trailing: Text(formatter.format(line.price.round())),
                                                  title: Container(
                                                    height: 50,
                                                    child: Row(
                                                      children: [Text(line.name)],
                                                    ),
                                                  ))));
                                    },
                                  ))
                            ],
                          );
                        } else if (completedPurchasedOrders.length > 0) {
                          return SingleChildScrollView(
                              child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Padding(
                                padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                                child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                              ),
                              ListView(
                                  padding: const EdgeInsets.all(8),
                                  shrinkWrap: true,
                                  children: List.generate(
                                    completedPurchasedOrders.length,
                                    (index) {
                                      Order order = completedPurchasedOrders[index];
                                      OrderLine line = order.items[0];
                                      return InkWell(
                                          onTap: () async {
                                            // show a loading indicator
                                            showDialog(
                                                context: context,
                                                builder: (BuildContext context) {
                                                  return Center(child: Loading());
                                                });

                                            // fetch the product
                                            Product product =
                                                await ResoldRest.getProduct(customer.token, line.productId);

                                            Navigator.of(context, rootNavigator: true).pop('dialog');

                                            // navigate to order details page
                                            Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        OrderDetails(order: order, product: product, isSeller: false)));
                                          },
                                          child: Card(
                                              child: ListTile(
                                                  trailing: Text(formatter.format(line.price.round())),
                                                  title: Container(
                                                    height: 50,
                                                    child: Row(
                                                      children: [Text(line.name)],
                                                    ),
                                                  ))));
                                    },
                                  ))
                            ],
                          ));
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
                              ListView(
                                  padding: const EdgeInsets.all(8),
                                  shrinkWrap: true,
                                  children: List.generate(
                                    inProgressSoldOrders.length,
                                    (index) {
                                      Order order = inProgressSoldOrders[index];
                                      OrderLine line = order.items[0];
                                      Duration pickupDifference;
                                      Duration dropoffDifference;
                                      if (order.status == 'pickup') {
                                        dropoffDifference = order.dropoffEta.difference(DateTime.now());
                                        pickupDifference = order.pickupEta.difference(DateTime.now());
                                      } else if (order.status == 'delivery_in_progress') {
                                        dropoffDifference = order.dropoffEta.difference(DateTime.now());
                                      } // end if pickup or delivery in progress
                                      return InkWell(
                                          onTap: () async {
                                            // show a loading indicator
                                            showDialog(
                                                context: context,
                                                builder: (BuildContext context) {
                                                  return Center(child: Loading());
                                                });

                                            // fetch the product
                                            Product product =
                                                await ResoldRest.getProduct(customer.token, line.productId);

                                            Navigator.of(context, rootNavigator: true).pop('dialog');

                                            // navigate to order details page
                                            Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        OrderDetails(order: order, product: product, isSeller: true)));
                                          },
                                          child: Card(
                                              child: ListTile(
                                                  trailing: Text(formatter.format(line.price.round())),
                                                  title: Container(
                                                    child: Column(
                                                      crossAxisAlignment: CrossAxisAlignment.start,
                                                      children: [
                                                        Text(line.name),
                                                        order.status == 'pickup'
                                                            ? Text(
                                                                'Driver arriving in ${pickupDifference.inMinutes} minutes',
                                                                style: TextStyle(color: Colors.grey, fontSize: 12))
                                                            : order.status == 'delivery_in_progress'
                                                                ? Text(
                                                                    'Delivery arriving in ${dropoffDifference.inMinutes} minutes',
                                                                    style: TextStyle(color: Colors.grey, fontSize: 12))
                                                                : SizedBox(),
                                                      ],
                                                    ),
                                                  ))));
                                    },
                                  )),
                              SizedBox(height: 10),
                              Padding(
                                padding: EdgeInsets.fromLTRB(20, 10, 0, 0),
                                child: Text('Delivered', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                              ),
                              ListView(
                                  padding: const EdgeInsets.all(8),
                                  shrinkWrap: true,
                                  children: List.generate(
                                    completedSoldOrders.length,
                                    (index) {
                                      Order order = completedSoldOrders[index];
                                      OrderLine line = order.items[0];
                                      return InkWell(
                                          onTap: () async {
                                            // show a loading indicator
                                            showDialog(
                                                context: context,
                                                builder: (BuildContext context) {
                                                  return Center(child: Loading());
                                                });

                                            // fetch the product
                                            Product product =
                                                await ResoldRest.getProduct(customer.token, line.productId);

                                            Navigator.of(context, rootNavigator: true).pop('dialog');

                                            // navigate to order details page
                                            Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        OrderDetails(order: order, product: product, isSeller: true)));
                                          },
                                          child: Card(
                                              child: ListTile(
                                                  trailing: Text(formatter.format(line.price.round())),
                                                  title: Container(
                                                    height: 50,
                                                    child: Row(
                                                      children: [Text(line.name)],
                                                    ),
                                                  ))));
                                    },
                                  ))
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
                              ListView(
                                  padding: const EdgeInsets.all(8),
                                  shrinkWrap: true,
                                  children: List.generate(
                                    inProgressSoldOrders.length,
                                    (index) {
                                      Order order = inProgressSoldOrders[index];
                                      OrderLine line = order.items[0];
                                      return InkWell(
                                          onTap: () async {
                                            // show a loading indicator
                                            showDialog(
                                                context: context,
                                                builder: (BuildContext context) {
                                                  return Center(child: Loading());
                                                });

                                            // fetch the product
                                            Product product =
                                                await ResoldRest.getProduct(customer.token, line.productId);

                                            Navigator.of(context, rootNavigator: true).pop('dialog');

                                            // navigate to order details page
                                            Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        OrderDetails(order: order, product: product, isSeller: true)));
                                          },
                                          child: Card(
                                              child: ListTile(
                                                  trailing: Text(formatter.format(line.price.round())),
                                                  title: Container(
                                                    height: 50,
                                                    child: Row(
                                                      children: [Text(line.name)],
                                                    ),
                                                  ))));
                                    },
                                  ))
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
                              ListView(
                                  padding: const EdgeInsets.all(8),
                                  shrinkWrap: true,
                                  children: List.generate(
                                    completedSoldOrders.length,
                                    (index) {
                                      Order order = completedSoldOrders[index];
                                      OrderLine line = order.items[0];
                                      return InkWell(
                                          onTap: () async {
                                            // show a loading indicator
                                            showDialog(
                                                context: context,
                                                builder: (BuildContext context) {
                                                  return Center(child: Loading());
                                                });

                                            // fetch the product
                                            Product product =
                                                await ResoldRest.getProduct(customer.token, line.productId);

                                            Navigator.of(context, rootNavigator: true).pop('dialog');

                                            // navigate to order details page
                                            Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        OrderDetails(order: order, product: product, isSeller: true)));
                                          },
                                          child: Card(
                                              child: ListTile(
                                                  trailing: Text(formatter.format(line.price.round())),
                                                  title: Container(
                                                    height: 50,
                                                    child: Row(
                                                      children: [Text(line.name)],
                                                    ),
                                                  ))));
                                    },
                                  ))
                            ],
                          ));
                        } // end if completed sold orders
                      }())
                    ])),
              );
            }));
  } // end function build
}
