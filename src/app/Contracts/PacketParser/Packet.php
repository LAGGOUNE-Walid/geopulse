<?php

namespace Pulse\Contracts\PacketParser;

use GeoJson\Geometry\Point;

interface Packet
{
    /**
     * Create a Packet instance from a string representation of the data.
     *
     * This method parses the provided string (which is expected to be a JSON message or similar format)
     * and populates the Packet object with the relevant information.
     *
     * @param  string  $data  The data (message) sent to the server by the client over UDP.
     * @return Packet Returns the instance of the Packet that has been populated with data from the string.
     */
    public function fromString(string $data): Packet;

    /**
     * Retrieve the App ID included in the packet.
     *
     * This method extracts the App ID from the packet
     *
     * @return string|null Returns the App ID as a string if available, or null if not set.
     */
    public function getAppId(): ?string;

    /**
     * Retrieve the Client ID included in the packet.
     *
     * This method extracts the Client ID from the packet
     *
     * @return string|null Returns the Client ID as a string if available, or null if not set.
     */
    public function getClientId(): ?string;

    /**
     * Convert the Packet instance to a GeoJSON Point object.
     *
     * This method transforms the Packet data into a GeoJSON Point object, which represents
     * geographical coordinates and is used for spatial data.
     *
     * @return Point|null Returns a GeoJSON Point object if conversion is possible, or null if not.
     */
    public function toPoint(): ?Point;

    /**
     * Convert the Packet instance to a JSON string.
     *
     * This method serializes the Packet data into a JSON string format, which is suitable for transmission
     * or storage. This typically includes all relevant packet information in JSON format.
     *
     * @return string Returns the serialized Packet data as a JSON string.
     */
    public function toJson(): string;

    /**
     * Convert the Packet instance to a php array.
     *
     * This method serializes the Packet data into a php array format
     *
     * @return array Returns the Packet data php array .
     */
    public function toArray(): array;
}
