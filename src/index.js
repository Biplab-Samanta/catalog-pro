/* global catalogproappLocalizer */
import React, { Component } from 'react';
import Select from 'react-select';
import axios from 'axios';
import './style.scss';
class Banner7 extends Component {
    constructor(props) {
        super(props);
        this.state = {
            items: [],
            is_msg_callback_open: false,
            write_msg_change: '',
            change_dropdown: '',
            first_choice: '',
            second_choice: '',
            others_choice: '',
            catalog_start_date_order: '',
            catalog_end_date_order: '',
        };

        this.woocommerce_catalog_enquiry_open_chat = this.woocommerce_catalog_enquiry_open_chat.bind(this);

        this.woocommerce_catalog_enquiry_send_reply = this.woocommerce_catalog_enquiry_send_reply.bind(this);

        this.write_msg_change = this.write_msg_change.bind(this);
        
        this.catalog_status_changes_dropdown_open = this.catalog_status_changes_dropdown_open.bind(this);
        
        this.StatusOnChange = this.StatusOnChange.bind(this);

        this.catalog_status_changes_dropdown_close = this.catalog_status_changes_dropdown_close.bind(this);

        this.onCatalogChange = this.onCatalogChange.bind(this);
        
        
    }

    onCatalogChange(e, name) {
        //console.log(name);
        this.setState({
            change_dropdown: name === 'second_choice' ? e.value : '',
            first_choice: name === 'first_choice' ? e.value : '',
            second_choice: name === 'second_choice' ? e.value : '',
            others_choice: name === 'others_choice' ? e.value : '',
            catalog_start_date_order: name === 'catalog_start_date_order' ? e.target.value : '',
            catalog_end_date_order: name === 'catalog_end_date_order' ? e.target.value : '', 
        });

        axios
            .get(
                `${catalogproappLocalizer.apiUrl}/mvx_catalog_pro/v1/fetch_enquiry_post_details`,
                {
                    params: { 
                        first_choice: name === 'first_choice' ? e.value : this.state.first_choice,
                        second_choice: name === 'second_choice' ? e.value : this.state.second_choice,
                        others_choice: name === 'others_choice' ? e.value : this.state.others_choice,
                        catalog_start_date_order: name === 'catalog_start_date_order' ? e.target.value : this.state.catalog_start_date_order,
                        catalog_end_date_order: name === 'catalog_end_date_order' ? e.target.value : this.state.catalog_end_date_order, 
                    },
                }
            )
            .then((response) => {
                this.setState({
                    items: response.data,
                });
            });



/*        axios({
            method: 'get',
            url: `${catalogproappLocalizer.apiUrl}/mvx_catalog_pro/v1/fetch_enquiry_post_details`,
            data: {
                first_choice: name === 'second_choice' ? e.value : '',
                second_choice: name === 'first_choice' ? e.value : '',
                others_choice: name === 'others_choice' ? e.value : '',
                catalog_start_date_order: name === 'catalog_start_date_order' ? e.target.value : '',
                catalog_end_date_order: name === 'catalog_end_date_order' ? e.target.value : '',
            },
        }).then((response) => {
            this.setState({
                items: response.data,
            });
        });*/


    }

    catalog_status_changes_dropdown_close(e) {

    }

    catalog_status_changes_dropdown_open(e) {

    }

    StatusOnChange(e, datas) {
        console.log(datas[0]);
    }

    woocommerce_catalog_enquiry_send_reply(e, data) {
        axios({
            method: 'post',
            url: `${catalogproappLocalizer.apiUrl}/mvx_catalog_pro/v1/update_msg_by_admin`,
            data: {
                data_value: data,
                text: this.state.write_msg_change,
            },
        }).then((response) => {
            axios({
                url: `${catalogproappLocalizer.apiUrl}/mvx_catalog_pro/v1/fetch_enquiry_post_details`,
            }).then((response1) => {
                this.setState({
                    items: response1.data,
                    write_msg_change: ''
                });
            });
        });
    }

    woocommerce_catalog_enquiry_open_chat(to_user_id, product_id, enquiry_id) {
        this.setState({
            is_msg_callback_open: true,
            open_enquiry_id: enquiry_id
        });
    }

    write_msg_change(e) {
        this.setState({
            write_msg_change: e.target.value
        })
    }

    componentDidMount() {
        axios({
            url: `${catalogproappLocalizer.apiUrl}/mvx_catalog_pro/v1/fetch_enquiry_post_details`,
        }).then((response) => {
            this.setState({
                items: response.data,
            });
        });

    }

    render() {
        return (
            <div className="messaging">
                <h1 className='title'>Enquiry Chat</h1>

 <div className="select-area-wrap">

 <div className="select-area-left">

                <Select
                    className="catalog-search-by-status"
                    options={catalogproappLocalizer.search_by_status}
                    onChange={(e) => {
                        this.onCatalogChange(
                            e, 'first_choice'
                        );
                    }}
                ></Select>

                <Select
                    className="catalog-search-by"
                    options={catalogproappLocalizer.search_by}
                    onChange={(e) => {
                        this.onCatalogChange(
                            e, 'second_choice'
                        );
                    }}
                ></Select>

                <Select
                    className="catalog-search"
                    options={this.state.change_dropdown && this.state.change_dropdown === 'product_name' ? catalogproappLocalizer.all_products : this.state.change_dropdown === 'customer_name' ? catalogproappLocalizer.all_users : catalogproappLocalizer.enquiry_titles}
                    onChange={(e) => {
                        this.onCatalogChange(
                            e, 'others_choice'
                        );
                    }}
                ></Select>
                </div>
 <div className="select-area-right">

                <input type="date" onChange={(e) => {
                        this.onCatalogChange(
                            e, 'catalog_start_date_order'
                        );
                    }} />
                <input type="date" onChange={(e) => {
                        this.onCatalogChange(
                            e, 'catalog_end_date_order'
                        );
                    }} />
</div>

</div>
{/* <!-- Filter section start --> */}

                  <div className="inbox-msg">
                    <div className="inbox-people">
                      <div className="inbox-chat">


                      {this.state.items.length > 0 ? this.state.items.map((student1, index1) => (

                        <div className={`chat-list chat-list_${index1}`} 
                        onClick={(e) =>
                            this.woocommerce_catalog_enquiry_open_chat(
                                student1.to_user_id, student1.product_id, student1.enquiry_id
                            )
                        }>

                            <div className="chat-people">
                              <div className="chat-img"> 
                                <p
                                dangerouslySetInnerHTML={{
                                    __html: student1.product_image,
                                }}
                                ></p>
                              </div>

                              <div className="chat-ib">
                                <h5>
                                    <p
                                        dangerouslySetInnerHTML={{
                                            __html: student1.product_name,
                                        }}
                                        >
                                    </p>

                                {
                                    student1.count > 0 ?
                                    <div className={`hide_unread${index1}`}>  <span className="chat-notify">{student1.count}</span></div>
                                    : ''
                                }

                                </h5>
                                <p className="admin-short-comming-msg">{student1.last_massage}</p>
                              </div>
                            </div>
                          </div>

                        )) : ''}

                      </div>

                    </div>


                    <div className="mesgs">
                    {this.state.is_msg_callback_open && this.state.items.length > 0 ? this.state.items.map((student2, index2) => (
                        <>
                        {this.state.open_enquiry_id === student2.enquiry_id ?
            <div className="chat-list chat-list-msg">
             

            <div className="chat-people">
              <div className="customerTitleArea">

                <div className="statusDrpDown">
                <p className="productLink">
                 
                    <span className="woo-catalog-quantity-class"> Product Quantity : {student2.quantity_number} </span>
                </p>
                  <label>
                    Status
                  </label>
                  <div className="cat-visiblity"

                    onClick={(e) =>
                        this.catalog_status_changes_dropdown_open(e)
                      } 

                    >
                    <span>
                    {student2.enquiry_current_status}
                    </span>
                    <ul className='enquiry-status-open'>

                    {Object.entries(student2.catalog_status).length > 0 ? Object.entries(student2.catalog_status).map(student4 => (
                        <>
                        <li>
                            <label>
                            <input type="radio" name="enquiry_status" id={`enquiry_status ${student4[0]}`}
                            onChange={(e) => {
                                this.StatusOnChange(e, student4);
                            }} 
                            />
                            <span for={`enquiry_status ${student4[0]}`} className="selectit">{student4[1]}</span>
                            </label>
                        </li>

                        </>

                    )) : ''}

                    <div className="status-changes-area">
                        <div className="statusButnArea">
                    
                            <button type="button"
                                 onClick={(e) =>
                                this.catalog_status_changes_dropdown_close(e)
                                }>
                            Cancel
                             </button>
                        </div>
                    </div>

                </ul>
                  </div>
                </div>

                


              </div>

              <div className="imgNameCls">
                <div className="chat-img">
                    <p
                    dangerouslySetInnerHTML={{
                        __html: student2.user_image,
                    }}
                    ></p>
           

                </div>

                <div className="chat-ib">
                    <p>
                    <span className='user-name'
                    dangerouslySetInnerHTML={{
                        __html: student2.user_name_and_email,
                    }}
                    ></span> | &nbsp;
                         <span className="productDetailCls">
                        {student2.enquiry_post_date}
                    </span>
                    </p>
                    <p className="productDetailCls">
                    {student2.enquiry_post_title}
                     
                    </p>
                </div>
              </div>

            </div>



            <div className="productEnquiryArea"> 
               
              

                <div className="enquiry-details-chat">    
                    
                   
                </div>


            </div>



            <div className="msg-history">
            { /*conversation*/ }

            {student2.conversation_lists.length > 0 ? student2.conversation_lists.map((student3, index3) => (
                <>
                {student3.value_c.from_user_id === student3.current_user_id ?
                <div className="outgoing-msg">
                    <div className="sent-msg">
                        <p>{student3.value_c.chat_message}</p>
                    <span className="time-date">{student3.date_format} | {student3.time_format}</span> 
                    </div>
                </div>

                : 

                <div className="incoming-msg">
                    <div className="received-msg">
                        <div className="received-withd-msg">
                            <p>{student3.value_c.chat_message}</p>
                            <span className="time-date">{student3.date_format} | {student3.time_format}</span>
                        </div>
                    </div>
                </div>

                }
                </>
            )) : 'No conversation found !'}
            </div>



            <div className="type-msg">
              <div className="input-msg-write">
                <textarea id="write_msg" name="write_msg" rows={1} cols={60} placeholder="Type Massage..." value={this.state.write_msg_change} onChange={this.write_msg_change} />
                
                <button className='send_button' type="button"
                  onClick={(e) =>
                    this.woocommerce_catalog_enquiry_send_reply(e, student2)
                  }
                >
                Send
                </button>
              </div>
            </div>

        </div>

        : ''}
        </>

                        
                    )) : ''}
                    </div>





        
        





                </div>
            </div>
        );
    }
}
export default Banner7;