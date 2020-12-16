import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/screens/messages/message.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';

class DeliveryQuoteList extends StatelessWidget {
  final CustomerResponse customer;
  final Position currentLocation;
  final List<FirebaseDeliveryQuote> quotes;
  final Function dispatcher;

  DeliveryQuoteList({this.customer, this.currentLocation, this.quotes, this.dispatcher});

  @override
  Widget build(BuildContext context) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      ListView(
          key: Key(quotes.length.toString()),
          padding: const EdgeInsets.all(8),
          physics: const NeverScrollableScrollPhysics(),
          shrinkWrap: true,
          children: List.generate(
            quotes.length,
            (index) {
              FirebaseDeliveryQuote quote = quotes[index];
              bool isSender = customer.id == quote.fromCustomer.id;
              bool isSeller = customer.id == quote.sellerCustomerId;

              return InkWell(
                  onTap: () async {
                    // show a loading indicator
                    showDialog(
                        context: context,
                        builder: (BuildContext context) {
                          return Center(child: Loading());
                        });

                    // navigate to message page
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (context) => MessagePage(
                                fromCustomer: customer,
                                toCustomer: quote.toCustomer,
                                currentLocation: currentLocation,
                                product: quote.product,
                                chatId: quote.chatId,
                                dispatcher: dispatcher)));

                    // mark the message as read
                    ResoldFirebase.markInboxMessageRead('${customer.id}-${quote.product.id.toString()}');

                    // hide loading indicator
                    Navigator.of(context, rootNavigator: true).pop('dialog');
                  },
                  child: Card(
                      child: ListTile(
                          leading: CircleAvatar(
                              backgroundImage:
                                  CachedNetworkImageProvider(baseProductImagePath + quote.product.thumbnail)),
                          title: Container(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(quote.product.name),
                                isSender
                                    ? Text(
                                        'You have sent a delivery request for ${isSeller ? quote.expectedPickup : quote.expectedDropoff}',
                                        style: TextStyle(color: Colors.grey, fontSize: 12))
                                    : Text(
                                        'You have received a delivery request for ${isSeller ? quote.expectedPickup : quote.expectedDropoff}',
                                        style: TextStyle(color: Colors.grey, fontSize: 12))
                              ],
                            ),
                          ))));
            },
          ))
    ]);
  } // end function build
}
