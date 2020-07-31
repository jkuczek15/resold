import 'package:flutter/material.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/resold.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:resold/constants/url-config.dart';
import 'package:intl/intl.dart';

class ProductPage extends StatefulWidget {
  Product product;

  ProductPage(Product product, {Key key}) : super(key: key) {
    this.product = product;
  }

  @override
  ProductPageState createState() => ProductPageState(this.product);
}

class ProductPageState extends State<ProductPage> {

  Product product;
  Future<List<String>> futureImages;

  ProductPageState(Product product) {
    this.product = product;
  }

  @override
  void initState() {
    super.initState();
    setState(() {
      futureImages = Resold.getProductImages(product.id);
    });
  }

  @override
  Widget build(BuildContext context) {
    var formatter = new NumberFormat("\$###,###", "en_US");
    return WillPopScope(
      child: Scaffold(
        appBar: AppBar (
          title: Text(product.name, style: new TextStyle(color: Colors.white)),
          backgroundColor: const Color(0xff41b8ea),
          iconTheme: IconThemeData(
            color: Colors.white, //change your color here
          ),
        ),
        body: Stack (
          children: [
            FutureBuilder<List<String>>(
                future: futureImages,
                builder: (context, snapshot) {
                  if (snapshot.hasData) {
                    if(snapshot.data.length == 1) {
                      return Padding (
                        padding: EdgeInsets.symmetric(horizontal: 10.0, vertical: 10.0),
                        child: FadeInImage(
                          image: NetworkImage(baseImagePath + snapshot.data[0]),
                          placeholder: AssetImage('assets/images/placeholder-image.png'),
                          fit: BoxFit.cover
                        )
                      );
                    } else {
                      return SingleChildScrollView (
                        child: Column (
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            CarouselSlider(
                                options: CarouselOptions(height: 400.0),
                                items: snapshot.data.map((image) {
                                  return Builder(
                                    builder: (BuildContext context) {
                                      return Container(
                                          width: MediaQuery.of(context).size.width,
                                          margin: EdgeInsets.symmetric(horizontal: 10.0, vertical: 10.0),
                                          decoration: BoxDecoration(
                                              color: const Color(0xff41b8ea)
                                          ),
                                          child: FadeInImage(image: NetworkImage(baseImagePath + image), placeholder: AssetImage('assets/images/placeholder-image.png'), fit: BoxFit.cover)
                                      );
                                    },
                                  );
                                }).toList()
                            ),
                            Padding(
                              padding: EdgeInsets.symmetric(horizontal: 10.0, vertical: 10.0),
                              child: Column (
                                children: [
                                  Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Column (
                                            mainAxisAlignment: MainAxisAlignment.start,
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Container(
                                                padding: new EdgeInsets.only(right: 13.0),
                                                width: 300,
                                                child: new Text(
                                                  product.name,
                                                  overflow: TextOverflow.fade,
                                                  style: new TextStyle(
                                                    fontSize: 14.0,
                                                    fontFamily: 'Roboto',
                                                    fontWeight: FontWeight.normal,
                                                  ),
                                                ),
                                              ),
                                              Container(
                                                padding: new EdgeInsets.only(right: 13.0),
                                                width: 300,
                                                child: new Text(
                                                  product.titleDescription ?? "",
                                                  overflow: TextOverflow.fade,
                                                  style: new TextStyle(
                                                    fontSize: 14.0,
                                                    fontFamily: 'Roboto',
                                                    fontWeight: FontWeight.normal,
                                                  ),
                                                ),
                                              ),
                                            ]
                                        ),
                                        Column (
                                            mainAxisAlignment: MainAxisAlignment.end,
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(formatter.format(double.parse(product.price).round()),
                                                  style: new TextStyle(
                                                    fontSize: 12.0,
                                                    fontFamily: 'Roboto',
                                                    fontWeight: FontWeight.bold,
                                                  )
                                              )
                                            ]
                                        )
                                      ]
                                  ),
                                  SizedBox(height: 20),
                                  Text(product.description.isNotEmpty ? product.description.replaceAll("<br />", "\n") : "")
                                ]
                              )
                            )
                          ],
                        )
                      );
                    }
                  } else {
                    return Column (
                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        Center(
                          child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea))
                        )
                      ]
                    );
                  }
                }
            )
          ],
        )
      ),
      onWillPop: () async {
        Navigator.pop(context);
        return false;
      }
    );
  }
}
