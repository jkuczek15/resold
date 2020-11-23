import 'package:flappy_search_bar/flappy_search_bar.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';
import 'package:resold/widgets/resold-search-bar.dart';
import 'package:resold/widgets/scroll/scrollable-filter-list.dart';

class MapPage extends StatefulWidget {
  MapPage({Key key}) : super(key: key);

  @override
  MapPageState createState() => MapPageState();
}

class MapPageState extends State<MapPage> {
  Future<Position> futureCurrentLocation;
  final Map<String, Marker> markers = {};
  List<Product> products = new List<Product>();
  Future<List<Product>> futureLocalProducts;
  String searchTerm = '';

  @override
  void initState() {
    super.initState();
    futureCurrentLocation = Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
  } // end function initState

  @override
  Widget build(BuildContext context) {
    return ViewModelSubscriber<AppState, CustomerResponse>(
        converter: (state) => state.customer,
        builder: (context, dispatcher, model) {
          return ViewModelSubscriber<AppState, SearchState>(
              converter: (state) => state.searchState,
              builder: (context, dispatcher, searchState) {
                return FutureBuilder<Position>(
                    future: futureCurrentLocation,
                    builder: (context, snapshot) {
                      if (snapshot.hasData) {
                        Position currentLocation = snapshot.data;
                        futureLocalProducts =
                            Search.fetchSearchProducts(searchTerm, currentLocation.latitude, currentLocation.longitude);
                        return LiquidPullToRefresh(
                          height: 80,
                          springAnimationDurationInMilliseconds: 500,
                          onRefresh: () {
                            // setState(() {});
                            // return futureLocalProducts = Search.fetchSearchProducts(
                            //     searchTerm, currentLocation.latitude, currentLocation.longitude);
                          },
                          showChildOpacityTransition: false,
                          color: ResoldBlue,
                          animSpeedFactor: 5.0,
                          child: FutureBuilder<List<Product>>(
                            future: futureLocalProducts,
                            builder: (context, snapshot) {
                              if (snapshot.hasData) {
                                return Stack(
                                  children: [
                                    Container(
                                        height: 650,
                                        child: GoogleMap(
                                          onMapCreated: (GoogleMapController controller) =>
                                              onMapCreated(controller, currentLocation),
                                          initialCameraPosition: CameraPosition(
                                            target: LatLng(currentLocation.latitude, currentLocation.longitude),
                                            zoom: 9.0,
                                          ),
                                          markers: markers.values.toSet(),
                                        )),
                                    Container(
                                      color: Colors.white.withOpacity(0.9),
                                      height: 130,
                                      child: ResoldSearchBar<Product>(
                                        textEditingController: searchState.searchBarController,
                                        header: ScrollableFilterList(searchState),
                                        hintText: 'Search entire marketplace here...',
                                        searchBarPadding: EdgeInsets.symmetric(horizontal: 20),
                                        cancellationWidget: Icon(Icons.cancel),
                                        onSearch: (term) {
                                          searchTerm = term;
                                          products = new List<Product>();
                                          return Search.fetchSearchProducts(
                                              term, currentLocation.latitude, currentLocation.longitude);
                                        },
                                        loader: Center(child: Loading()),
                                        suggestions: snapshot.data,
                                        onItemFound: (Product product, int index) {
                                          products.add(product);
                                          return SizedBox();
                                        },
                                        emptyWidget: Center(child: Text('Your search returned no results.')),
                                      ),
                                    ),
                                  ],
                                );
                              } else {
                                return Center(child: Loading());
                              } // end if we have data
                            },
                          ),
                        );
                      } else {
                        return Center(child: Loading());
                      } // end if snapshot has data
                    });
              });
        });
  } // end function build

  Future<void> onMapCreated(GoogleMapController controller, Position currentLocation) async {
    setState(() {
      markers.clear();

      final String currentLocationTitle = "You";
      final currentLocationMarker = Marker(
        markerId: MarkerId(currentLocationTitle),
        position: LatLng(currentLocation.latitude, currentLocation.longitude),
        icon: BitmapDescriptor.defaultMarkerWithHue(198),
        infoWindow: InfoWindow(
          title: currentLocationTitle,
        ),
      );

      markers[currentLocationTitle] = currentLocationMarker;
    });
  } // end function onMapCreated
}
