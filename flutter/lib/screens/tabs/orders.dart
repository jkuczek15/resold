import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/models/order.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/widgets/loading.dart';
import 'package:intl/intl.dart';

class OrdersPage extends StatefulWidget {
  final CustomerResponse customer;

  OrdersPage(customer, {Key key}) : customer = customer, super(key: key);

  @override
  OrdersPageState createState() => OrdersPageState(customer);
}

class OrdersPageState extends State<OrdersPage> {

  Future<List<Order>> futurePurchasedOrders;
  Future<List<Order>> futureSoldOrders;
  final CustomerResponse customer;

  OrdersPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
    futurePurchasedOrders = Magento.getPurchasedOrders(98);
    futureSoldOrders = Magento.getPurchasedOrders(customer.id);
  }

  @override
  Widget build(BuildContext context) {
    var formatter = new NumberFormat("\$###,###", "en_US");
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
            backgroundColor: Colors.white,
            toolbarHeight: 74,
            bottom: TabBar(
              indicatorColor: const Color(0xff41b8ea),
              tabs: [
                Tab(icon: Icon(MdiIcons.cart, semanticLabel: 'Purchased'), text: 'Purchased'),
                Tab(icon: Icon(MdiIcons.clipboardText, semanticLabel: 'Sold'), text: 'Sold')
              ],
            )
        ),
        body: TabBarView(
          children: [
            FutureBuilder<List<Order>>(
              future: futurePurchasedOrders,
              builder: (context, snapshot) {
                if (snapshot.hasData && snapshot.data.length > 0) {
                    return ListView (
                      padding: const EdgeInsets.all(8),
                      children: List.generate(snapshot.data.length, (index) {
                        Order order = snapshot.data[index];
                        OrderLine line = order.items[0];
                        return Card (
                            child: ListTile (
                              trailing: Text(formatter.format(line.price.round())),
                              title: Container(
                                height: 50,
                                child: Row (
                                  children: [
                                    Text(line.name)
                                  ],
                                ),
                              )
                            )
                          );
                      }),
                    );
                } else if(snapshot.hasData && snapshot.data.length == 0) {
                  return Center(child: Text('You have not purchased any items.'));
                }
                else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                // By default, show a loading spinner.
                return Center(child: Loading());
              },
            ),
            FutureBuilder<List<Order>>(
              future: futureSoldOrders,
              builder: (context, snapshot) {
                if (snapshot.hasData && snapshot.data.length > 0) {
                  return ListView (
                    padding: const EdgeInsets.all(8),
                    children: List.generate(snapshot.data.length, (index) {
                      Order order = snapshot.data[index];
                      OrderLine line = order.items[0];
                      return Card (
                          child: ListTile (
                              trailing: Text(formatter.format(line.price.round())),
                              title: Container(
                                height: 50,
                                child: Row (
                                  children: [
                                    Text(line.name)
                                  ],
                                ),
                              )
                          )
                      );
                    }),
                  );
                } else if(snapshot.hasData && snapshot.data.length == 0) {
                  return Center(child: Text('You have not sold any items.'));
                } else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                // By default, show a loading spinner.
                return Center(child: Loading());
              },
            )
          ],
        ),
      ),
    );
  }
}
