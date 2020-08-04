import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/resold.dart';

class AccountPage extends StatefulWidget {
  final CustomerResponse customer;

  AccountPage(CustomerResponse customer, {Key key}) : customer = customer, super(key: key);

  @override
  AccountPageState createState() => AccountPageState(customer);
}

class AccountPageState extends State<AccountPage> {

  Future<List<Product>> futureForSaleVendorProducts;
  Future<List<Product>> futureSoldVendorProducts;
  final CustomerResponse customer;

  AccountPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
    futureForSaleVendorProducts = Resold.getVendorProducts(customer.vendorId, 'for-sale');
    futureSoldVendorProducts = Resold.getVendorProducts(customer.vendorId, 'sold');
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          backgroundColor: Colors.white,
          toolbarHeight: 75,
          bottom: TabBar(
            tabs: [
              Tab(icon: Icon(MdiIcons.signRealEstate, semanticLabel: 'For Sale'), text: 'For Sale'),
              Tab(icon: Icon(MdiIcons.clipboardText, semanticLabel: 'Sold'), text: 'Sold')
            ],
          )
        ),
        body: TabBarView(
          children: [
            FutureBuilder<List<Product>>(
              future: futureForSaleVendorProducts,
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  return Text('success');
                } else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                // By default, show a loading spinner.
                return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
              },
            ),
            FutureBuilder<List<Product>>(
              future: futureSoldVendorProducts,
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  return Text('success');
                } else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                // By default, show a loading spinner.
                return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
              },
            )
          ],
        ),
      ),
    );
  }
}
