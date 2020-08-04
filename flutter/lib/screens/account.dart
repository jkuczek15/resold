import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/builders/product-list-builder.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/resold.dart';
import 'package:geolocator/geolocator.dart';

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
  Position currentLocation;

  AccountPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
    futureForSaleVendorProducts = Resold.getVendorProducts(customer.vendorId, 'for-sale');
    futureSoldVendorProducts = Resold.getVendorProducts(customer.vendorId, 'sold');
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      if(this.mounted) {
        setState(() {
          currentLocation = location;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return ListView(children: [
      Container(
          color: Colors.orangeAccent,
          height: 150.0,
          child: Center(child: Text('Something'))),
      DefaultTabController(
          length: 2,
          initialIndex: 0,
          child: Column(
            children: [
                TabBar(
                indicatorColor: const Color(0xff41b8ea),
                tabs: [
                  Tab(icon: Icon(MdiIcons.signRealEstate, semanticLabel: 'For Sale'), text: 'For Sale'),
                  Tab(icon: Icon(MdiIcons.clipboardText, semanticLabel: 'Sold'), text: 'Sold')
                ],
              ),
              Container (
                height: 300.0,
                child: TabBarView(
                children: [
                  FutureBuilder<List<Product>>(
                    future: futureForSaleVendorProducts,
                    builder: (context, snapshot) {
                      if (snapshot.hasData) {
                        return GridView.count(
                            crossAxisCount: 2,
                            childAspectRatio: 1.6,
                            children: List.generate(snapshot.data.length, (index) {
                              var product = snapshot.data[index];
                              return ProductListBuilder.buildProductGridTile(context, currentLocation, product, index);
                            })
                        );
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
                        return GridView.count(
                          crossAxisCount: 2,
                          childAspectRatio: 3/2,
                          children: List.generate(snapshot.data.length, (index) {
                            var product = snapshot.data[index];
                            return ProductListBuilder.buildProductGridTile(context, currentLocation, product, index);
                          }),
                        );
                      } else if (snapshot.hasError) {
                        return Text("${snapshot.error}");
                      }
                      // By default, show a loading spinner.
                      return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                    },
                  )
                ],
                )
              )
            ],
        ))
    ]);
  }
}
