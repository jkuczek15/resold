import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/models/product.dart';
import 'package:resold/screens/messages/message.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';

class DeliveryQuoteList extends StatelessWidget {
  final CustomerResponse customer;
  final Position currentLocation;
  final List<FirebaseDeliveryQuote> quotes;
  final Widget header;
  final Function dispatcher;

  DeliveryQuoteList({this.customer, this.currentLocation, this.quotes, this.header, this.dispatcher});

  @override
  Widget build(BuildContext context) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      header,
      ListView(
          padding: const EdgeInsets.all(8),
          physics: const NeverScrollableScrollPhysics(),
          shrinkWrap: true,
          children: List.generate(quotes.length, (index) {
            FirebaseDeliveryQuote quote = quotes[index];

            return FutureBuilder<Product>(
              future: ResoldRest.getProduct(customer.token, quote.productId),
              builder: (context, snapshot) {
                Product product = snapshot.data;
                return InkWell(
                    onTap: () async {
                      // show a loading indicator
                      showDialog(
                          context: context,
                          builder: (BuildContext context) {
                            return Center(child: Loading());
                          });

                      if (!snapshot.hasData) {
                        product = await ResoldRest.getProduct(customer.token, quote.productId);
                      } // end if we don't have product data

                      // get the to customer details
                      CustomerResponse toCustomer = await Magento.getCustomerById(quote.idTo);

                      // navigate to message page
                      Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (context) => MessagePage(
                                  fromCustomer: customer,
                                  toCustomer: toCustomer,
                                  currentLocation: currentLocation,
                                  product: product,
                                  chatId: quote.chatId,
                                  dispatcher: dispatcher)));

                      // hide loading indicator
                      Navigator.of(context, rootNavigator: true).pop('dialog');
                    },
                    child: Card(
                        child: ListTile(
                            leading: CircleAvatar(
                              backgroundImage: snapshot.hasData
                                  ? CachedNetworkImageProvider(baseProductImagePath + product.thumbnail)
                                  : AssetImage('assets/placeholder-image.png'),
                            ),
                            title: Container(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  snapshot.hasData ? Text(product.name) : Text('...'),
                                ],
                              ),
                            ))));
              },
            );
          }))
    ]);
  } // end function build
}
