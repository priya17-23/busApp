vonage.messages.send({
  to: MESSAGES_TO_NUMBER,
  from: WHATSAPP_SENDER_ID,
  channel: Channels.WHATSAPP,
  messageType: 'custom',
  custom: {
    type: 'location',
    location: {
      longitude: -122.425332,
      latitude: 37.758056,
      name: 'Facebook HQ',
      address: '1 Hacker Way, Menlo Park, CA 94025',
    },
  },
})
  .then(({ messageUUID }) => console.log(messageUUID))
  .catch((error) => console.error(error));