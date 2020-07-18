import 'package:flutter/material.dart';
import '../models/product.dart';

class ProductListBuilder {

  static String baseImagePath = 'https://s3-us-west-2.amazonaws.com/resold-photos/catalog/product';

  static ListView buildProductList(List<Product> data) {
    return ListView.builder(
      itemCount: data.length,
      itemBuilder: (context, index) {
        return buildProductTile(data[index]);
      },
    );
  }

  static ListTile buildProductTile(Product data) {
    return ListTile(
        title: Card(
          child: InkWell(
              splashColor: Colors.blue.withAlpha(30),
              onTap: () { /* ... */ },
              child: Container(
                decoration: BoxDecoration(color: Colors.white),
                child: Container (
                    padding: EdgeInsets.fromLTRB(25, 25, 25, 25),
                    child: Column (
                      children: [
                        Row (
                          children: [
                            Column(
                              children: [
                                Align(
                                    alignment: Alignment.center,
                                    child: SizedBox (
                                        height: 300,
                                        width: 300,
                                        child: Image.network(baseImagePath + data.thumbnail, fit: BoxFit.cover)
                                    )
                                ),
                                SizedBox(height: 10),
                              ]
                            )
                          ]
                        ),
                        Row (
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(data.name),
                                Text("\$" + double.parse(data.price).round().toString())
                              ]
                            )
                          ]
                        )
                      ]
                    )
                ),
              )
          ),
        )
    );
  }
}