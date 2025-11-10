import { useMutation } from "convex/react";
import { api } from "../../convex/_generated/api";
import { useState } from "react";
import { toast } from "react-toastify";
import { Input } from "@mui/material";

export default function AddUserForm() {
    const [loading, setLoading]=useState(false);
    const [email, setEmail]=useState("");
    const [password, setPassword]=useState("");
    const [fullName, setFullName]=useState("");
     const addUser = useMutation(api.users.addUser);
    
    return (
        <div className="p-5">
            <form
            onSubmit={(event)=>{
                setLoading(true);
                event.preventDefault();
              
                addUser({
                    fullName:fullName,
                    email: email,
                    password: password,
                }).then(()=>{
                    toast.success("User added successfully!");
                    setLoading(false);
                }).catch((error)=>{
                    toast.error("Failed to add user: " + error.message);
                    setLoading(false);
                });
            }}
            className='flex flex-col gap-4'>
                <Input onChange={(e)=>{setFullName(e.target.value)}} type="text" placeholder='Full Name' className='p-2  rounded-md'/>
                <Input onChange={(e)=>{setEmail(e.target.value)}} type="email" placeholder='Email' className='p-2  rounded-md'/>
                <Input onChange={(e)=>{setPassword(e.target.value)}} type="password" placeholder='Password' className='p-2  rounded-md'/>
                <button type="submit" className='bg-amber-500 text-white p-2 rounded-md mt-4'>
                    {loading ? 'Adding user...' : 'Add User'}
                </button>
            </form>
        </div>
    )
}