import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:resold/models/order.dart';
import 'package:resold/models/product.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class OrderDetails extends StatefulWidget {
  final Order order;
  final Product product;
  final CustomerResponse customer;

  OrderDetails(customer, order, product, {Key key})
      : customer = customer,
        order = order,
        product = product,
        super(key: key);

  @override
  OrderDetailsState createState() =>
      OrderDetailsState(customer, order, product);
}

class OrderDetailsState extends State<OrderDetails> {
  final Order order;
  final Product product;
  final CustomerResponse customer;

  OrderDetailsState(CustomerResponse customer, Order order, Product product)
      : customer = customer,
        order = order,
        product = product;

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
          appBar: AppBar(
            title: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Align(
                    alignment: Alignment.centerLeft,
                    child: Container(
                        width: 250,
                        child: Text('Order - ' + product.name,
                            overflow: TextOverflow.ellipsis,
                            style: new TextStyle(color: Colors.white))))
              ],
            ),
            iconTheme: IconThemeData(
              color: Colors.white, //change your color here
            ),
            backgroundColor: const Color(0xff41b8ea),
          ),
          body: Text(product.deliveryId)),
    );
  }
}
