import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/models/order.dart';
import 'package:resold/models/product.dart';
import 'package:resold/screens/order/details.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';

class OrderList extends StatelessWidget {
  final CustomerResponse customer;
  final List<Order> orders;

  OrderList({this.customer, this.orders});

  @override
  Widget build(BuildContext context) {
    return ListView(
        padding: const EdgeInsets.all(8),
        physics: const AlwaysScrollableScrollPhysics(),
        shrinkWrap: true,
        children: List.generate(
          orders.length,
          (index) {
            Order order = orders[index];
            OrderLine line = order.items[0];
            Duration difference;

            if (order.status == 'pickup' || order.status == 'delivery_in_progress') {
              difference = order.dropoffEta.difference(DateTime.now());
            } // end if pickup or delivery in progress

            return FutureBuilder<Product>(
              future: ResoldRest.getProduct(customer.token, line.productId),
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  Product product = snapshot.data;
                  return InkWell(
                      onTap: () async {
                        // show a loading indicator
                        showDialog(
                            context: context,
                            builder: (BuildContext context) {
                              return Center(child: Loading());
                            });

                        // navigate to order details page
                        Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (context) => OrderDetails(order: order, product: product, isSeller: false)));

                        // hide loading indicator
                        Navigator.of(context, rootNavigator: true).pop('dialog');
                      },
                      child: Card(
                          child: ListTile(
                              leading: CircleAvatar(
                                backgroundImage: CachedNetworkImageProvider(baseProductImagePath + product.thumbnail),
                              ),
                              title: Container(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(line.name),
                                    order.status == 'pickup' || order.status == 'delivery_in_progress'
                                        ? Text('Arriving in ${difference.inMinutes} minutes',
                                            style: TextStyle(color: Colors.grey, fontSize: 12))
                                        : Text('Delivered: ' + DateFormat('EEEE M/d').format(order.created),
                                            style: TextStyle(color: Colors.grey, fontSize: 12))
                                  ],
                                ),
                              ))));
                } else {
                  return InkWell(
                      onTap: () async {
                        // show a loading indicator
                        showDialog(
                            context: context,
                            builder: (BuildContext context) {
                              return Center(child: Loading());
                            });

                        // fetch product on tap
                        Product product = await ResoldRest.getProduct(customer.token, line.productId);

                        // navigate to order details page
                        Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (context) => OrderDetails(order: order, product: product, isSeller: false)));

                        // hide loading indicator
                        Navigator.of(context, rootNavigator: true).pop('dialog');
                      },
                      child: Card(
                          child: ListTile(
                              leading: CircleAvatar(
                                backgroundImage: AssetImage('assets/placeholder-image.png'),
                              ),
                              title: Container(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(line.name),
                                    order.status == 'pickup' || order.status == 'delivery_in_progress'
                                        ? Text('Arriving in ${difference.inMinutes} minutes',
                                            style: TextStyle(color: Colors.grey, fontSize: 12))
                                        : Text('Delivered: ' + DateFormat('EEEE M/d').format(order.created),
                                            style: TextStyle(color: Colors.grey, fontSize: 12))
                                  ],
                                ),
                              ))));
                } // end if we have data
              },
            );
          },
        ));
  } // end function build
}
